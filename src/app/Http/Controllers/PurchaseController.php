<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;   // ← 解析ツールが型を追える
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\PurchaseRequest;
use App\Models\Item;
use App\Models\Order;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class PurchaseController extends Controller
{
	/** その商品は売却済み？（関係メソッドの有無で判定） */
	private function isSold(Item $item): bool
	{
		return $item->order()->exists();
	}

	/** 配送先（セッション優先／なければプロフィール） */
	private function shipto(Item $item): array
	{
		$u = Auth::user();
		return session("shipto.item_{$item->id}") ?? [
			'postal_code' => $u->postal_code,
			'address'     => $u->address,
			'building'    => $u->building,
		];
	}

	/** 配送先の表示用文字列（orders.address に保存する想定） */
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
		// 売却済み or 出品者本人なら詳細へ戻す
		if ($this->isSold($item) || $item->user_id === Auth::id()) {
			return redirect()->route('items.detail', $item);
		}

		$address = $this->shipto($item);
		return view('purchase.confirm', compact('item', 'address'));
	}

	/** 購入実行：コンビニは即確定／カードはStripe */
	public function store(PurchaseRequest $request, Item $item)
	{
		// URL直叩き対策：売却済み／出品者本人は弾く
		if ($this->isSold($item) || $item->user_id === Auth::id()) {
			return redirect()->route('items.detail', $item);
		}

		$ship    = $this->shipto($item);
		$address = $this->shiptoText($ship);

		/* -----------------------------
           コンビニ払い：Stripe使わず即確定
        ------------------------------*/
		if ($request->payment === Order::PAYMENT_KONBINI) {
			try {
				DB::transaction(function () use ($item, $address) {
					Order::firstOrCreate(
						['item_id' => $item->id], // 冪等に作成
						[
							'user_id' => Auth::id(),
							'address' => $address,               // 文字列で保持する設計
							'payment' => Order::PAYMENT_KONBINI,
							'status'  => Order::STATUS_PAID,     // 支払済みで確定
						]
					);
				});
			} catch (\Throwable $e) {
				Log::error('Konbini order create failed', [
					'e' => $e->getMessage(),
					'item_id' => $item->id,
				]);
				return redirect()
					->route('purchase.confirm', $item)
					->with('error', '購入処理に失敗しました。時間をおいて再度お試しください。');
			}

			// 一時住所はクリア
			session()->forget("shipto.item_{$item->id}");

			return redirect()
				->route('items.index')
				->with('success', '購入が完了しました（コンビニ払い）');
		}

		/* -----------------------------
           カード払い：Stripe Checkout
        ------------------------------*/
		Stripe::setApiKey(config('services.stripe.secret'));

		// Stripe要件：絶対URLで
		$successUrl = route('purchase.complete', [], true) . '?session_id={CHECKOUT_SESSION_ID}';

		$checkout = StripeSession::create([
			'mode'                 => 'payment',
			'payment_method_types' => ['card'], // コンビニ分岐済みなのでカード固定
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
			// 完了ハンドラで使う値
			'metadata' => [
				'item_id' => (string) $item->id,
				'user_id' => (string) Auth::id(),
				'address' => $address,
				'payment' => (string) Order::PAYMENT_CARD,
			],
		]);

		// 一時住所はここで破棄（戻るときは address_edit から再設定できる設計）
		session()->forget("shipto.item_{$item->id}");

		return redirect()->away($checkout->url);
	}

	/** 決済完了（カード決済） */
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

		// 即時決済のみここで確定（未決済は弾く）
		if (($session->payment_status ?? null) !== 'paid') {
			return redirect()->route('items.index');
		}

		$meta    = (object) ($session->metadata ?? []);
		$itemId  = (int)   ($meta->item_id ?? 0);
		$userId  = (int)   ($meta->user_id ?? 0);
		$address = (string)($meta->address ?? '');
		$payment = (string)($meta->payment ?? Order::PAYMENT_CARD);

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
						'payment' => $payment,
						'status'  => Order::STATUS_PAID,
					]
				);
			});
		} catch (\Throwable $e) {
			Log::error('Order create failed', [
				'e' => $e->getMessage(),
				'item_id' => $itemId,
				'user_id' => $userId,
			]);
		}

		return redirect()->route('items.index');
	}
}
