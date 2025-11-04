<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PurchaseRequest;
use App\Models\Item;
use App\Models\Order;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class PurchaseController extends Controller
{
	/** 共通: その商品は売却済み？ */
	private function isSold(Item $item): bool
	{
		return $item->order()->exists();
	}

	/** 共通: 配送先（セッション or プロフィール） */
	private function shipto(Item $item): array
	{
		$user = Auth::user();

		return session("shipto.item_{$item->id}") ?? [
			'postal_code' => $user->postal_code,
			'address'     => $user->address,
			'building'    => $user->building,
		];
	}

	/** 共通: 配送先の見出し文字列 */
	private function shiptoText(array $ship): string
	{
		return trim(sprintf(
			'〒%s %s %s',
			$ship['postal_code'] ?? '',
			$ship['address']     ?? '',
			$ship['building']    ?? ''
		));
	}

	/** 購入確認画面 */
	public function confirm(Item $item)
	{
		// 売却済み or 自分の商品は詳細へ戻す
		if ($this->isSold($item) || $item->user_id === Auth::id()) {
			return redirect()->route('items.detail', $item);
		}

		$address = $this->shipto($item);
		return view('purchase.confirm', compact('item', 'address'));
	}

	/** Stripe チェックアウト作成 → リダイレクト */
	public function store(PurchaseRequest $request, Item $item)
	{
		// 二重バリデーション（URL直叩き対策）
		if ($this->isSold($item) || $item->user_id === $request->user()->id) {
			return redirect()->route('items.detail', $item);
		}

		$ship    = $this->shipto($item);
		$address = $this->shiptoText($ship);

		Stripe::setApiKey(config('services.stripe.secret'));

		// 成功時の復帰 URL は絶対 URL で（Stripe 要件）
		$successUrl = route('purchase.complete', [], true) . '?session_id={CHECKOUT_SESSION_ID}';

		$checkout = StripeSession::create([
			'mode'                 => 'payment',
			'payment_method_types' => [$request->payment],   // 'card' / 'konbini' 等
			'line_items' => [[
				'price_data' => [
					'currency'    => 'jpy',
					'unit_amount' => $item->price,
					'product_data' => ['name' => $item->name],
				],
				'quantity' => 1,
			]],
			'customer_email' => $request->user()->email,
			'success_url'    => $successUrl,
			'cancel_url'     => route('purchase.confirm', $item),
			// complete() で確定に使う情報を埋め込む
			'metadata' => [
				'item_id' => (string) $item->id,
				'user_id' => (string) $request->user()->id,
				'address' => $address,
				'payment' => (string) $request->payment,
			],
		]);

		// 一時住所は破棄
		session()->forget("shipto.item_{$item->id}");

		return redirect()->away($checkout->url);
	}

	/** 決済完了ハンドラ（即時決済系） */
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
			Log::warning('Stripe session retrieve failed', ['e' => $e->getMessage(), 'session_id' => $sessionId]);
			return redirect()->route('items.index');
		}

		// 即時決済のみここで確定（コンビニ等は Webhook で確定させるのが通常）
		if (($session->payment_status ?? null) !== 'paid') {
			return redirect()->route('items.index');
		}

		$meta    = (object) ($session->metadata ?? []);
		$itemId  = (int)   ($meta->item_id ?? 0);
		$userId  = (int)   ($meta->user_id ?? 0);
		$address = (string)($meta->address ?? '');
		$payment = (string)($meta->payment ?? '');

		if ($itemId === 0 || $userId === 0) {
			return redirect()->route('items.index');
		}

		// 冪等に orders を作成（item_id ユニーク推奨）
		try {
			DB::transaction(function () use ($itemId, $userId, $address, $payment) {
				Order::firstOrCreate(
					['item_id' => $itemId],
					[
						'user_id' => $userId,
						'address' => $address,
						'payment' => $payment !== '' ? $payment : Order::PAYMENT_CARD,
						'status'  => Order::STATUS_PAID,
					]
				);
			});
		} catch (\Throwable $e) {
			Log::error('Order create failed', ['e' => $e->getMessage(), 'item_id' => $itemId, 'user_id' => $userId]);
		}

		return redirect()->route('items.index');
	}
}
