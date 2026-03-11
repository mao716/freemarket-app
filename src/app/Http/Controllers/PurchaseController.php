<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PurchaseRequest;
use App\Models\Item;
use App\Models\Order;
use App\Models\Trade;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class PurchaseController extends Controller
{
	private function isSold(Item $item): bool
	{
		return $item->order()->exists();
	}

	private function shipTo(Item $item): array
	{
		$user = Auth::user();

		return session("shipTo.item_{$item->id}") ?? [
			'postal_code' => $user->postal_code,
			'address'     => $user->address,
			'building'    => $user->building,
		];
	}

	private function shipToText(array $ship): string
	{
		return trim(sprintf(
			'〒%s %s %s',
			$ship['postal_code'] ?? '',
			$ship['address']     ?? '',
			$ship['building']    ?? ''
		));
	}

	private function createOrderAndTrade(Item $item, int $userId, string $address, string $payment): void
	{
		DB::transaction(function () use ($item, $userId, $address, $payment) {
			$order = Order::firstOrCreate(
				['item_id' => $item->id],
				[
					'user_id' => $userId,
					'address' => $address,
					'payment' => $payment,
					'status'  => Order::STATUS_PAID,
				]
			);

			Trade::firstOrCreate(
				['order_id' => $order->id],
				[
					'buyer_id'            => $userId,
					'seller_id'           => $item->user_id,
					'status'              => Trade::STATUS_IN_PROGRESS,
					'last_message_at'     => null,
					'buyer_unread_count'  => 0,
					'seller_unread_count' => 0,
				]
			);
		});
	}

	public function confirm(Item $item)
	{
		if ($this->isSold($item) || $item->user_id === Auth::id()) {
			return redirect()->route('items.detail', $item);
		}

		$address = $this->shipTo($item);
		return view('purchase.confirm', compact('item', 'address'));
	}

	public function store(PurchaseRequest $request, Item $item)
	{
		if ($this->isSold($item) || $item->user_id === Auth::id()) {
			return redirect()->route('items.detail', $item);
		}

		$ship    = $this->shipTo($item);
		$address = $this->shipToText($ship);

		if ($request->payment === Order::PAYMENT_KONBINI) {
			try {
				$this->createOrderAndTrade(
					$item,
					Auth::id(),
					$address,
					Order::PAYMENT_KONBINI
				);
			} catch (\Throwable $e) {
				Log::error('Konbini order create failed', [
					'e' => $e->getMessage(),
					'item_id' => $item->id,
				]);

				return redirect()
					->route('purchase.confirm', $item)
					->with('error', '購入処理に失敗しました。時間をおいて再度お試しください。');
			}

			session()->forget("shipTo.item_{$item->id}");

			return redirect()
				->route('items.index')
				->with('success', '購入が完了しました（コンビニ払い）');
		}

		Stripe::setApiKey(config('services.stripe.secret'));

		$successUrl = route('purchase.complete', [], true) . '?session_id={CHECKOUT_SESSION_ID}';

		$checkout = StripeSession::create([
			'mode'                 => 'payment',
			'payment_method_types' => ['card'],
			'line_items' => [[
				'price_data' => [
					'currency'    => 'jpy',
					'unit_amount' => $item->price,
					'product_data' => ['name' => $item->name],
				],
				'quantity' => 1,
			]],
			'customer_email' => Auth::user()->email,
			'success_url'    => $successUrl,
			'cancel_url'     => route('purchase.confirm', $item),
			'metadata' => [
				'item_id' => (string) $item->id,
				'user_id' => (string) Auth::id(),
				'address' => $address,
				'payment' => (string) Order::PAYMENT_CARD,
			],
		]);

		return redirect()->away($checkout->url);
	}

	public function complete(Request $request)
	{
		$sessionId = (string) $request->query('session_id', '');

		if ($sessionId === '') {
			return redirect()->route('items.index');
		}

		Stripe::setApiKey(config('services.stripe.secret'));

		try {
			$session = StripeSession::retrieve($sessionId);
		} catch (\Throwable $e) {
			Log::warning('Stripe session retrieve failed', [
				'e' => $e->getMessage(),
				'session_id' => $sessionId,
			]);

			return redirect()->route('items.index');
		}

		if (($session->payment_status ?? null) !== 'paid') {
			return redirect()->route('items.index');
		}

		$meta = (object) ($session->metadata ?? []);
		$itemId = (int) ($meta->item_id ?? 0);
		$userId = (int) ($meta->user_id ?? 0);
		$address = (string) ($meta->address ?? '');
		$payment = (string) ($meta->payment ?? Order::PAYMENT_CARD);

		if ($itemId === 0 || $userId === 0) {
			return redirect()->route('items.index');
		}

		try {
			$item = Item::findOrFail($itemId);

			$this->createOrderAndTrade(
				$item,
				$userId,
				$address,
				$payment
			);

			session()->forget("shipTo.item_{$itemId}");
		} catch (\Throwable $e) {
			Log::error('Order and trade create failed', [
				'e' => $e->getMessage(),
				'item_id' => $itemId,
				'user_id' => $userId,
			]);
		}

		return redirect()->route('items.index');
	}
}
