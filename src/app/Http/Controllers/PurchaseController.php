<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\PurchaseRequest;
use App\Models\Item;
use App\Models\Order;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class PurchaseController extends Controller
{
	public function confirm(Item $item)
	{
		$user = auth()->user();
		$sessionKey = "shipto.item_{$item->id}";
		$shipto = session($sessionKey) ?? [
			'postal_code' => $user->postal_code,
			'address'     => $user->address,
			'building'    => $user->building,
		];

		$address = $shipto;
		return view('purchase.confirm', compact('item', 'address'));
	}

	public function store(PurchaseRequest $request, Item $item)
	{
		$user = $request->user();

		// 売却済みチェック
		if ($item->order()->exists()) {
			return redirect()
				->route('items.detail', $item)
				->with('error', 'この商品はすでに購入されています。');
		}

		// 自分の出品チェック
		if ($item->user_id === $user->id) {
			return redirect()
				->route('items.detail', $item)
				->with('error', '自分が出品した商品は購入できません。');
		}

		// Stripe APIキー設定（.envのSTRIPE_SECRETを使用）
		Stripe::setApiKey(config('services.stripe.secret'));

		// ★ 決済成功後に戻るURLに {CHECKOUT_SESSION_ID} を付ける
		$successUrl = route('purchase.complete') . '?session_id={CHECKOUT_SESSION_ID}';

		// Checkoutセッション作成
		$checkout = StripeSession::create([
			'payment_method_types' => [$request->payment], // 'card' or 'konbini'
			'mode' => 'payment',
			'line_items' => [[
				'price_data' => [
					'currency' => 'jpy',
					'unit_amount' => $item->price,
					'product_data' => [
						'name' => $item->name,
					],
				],
				'quantity' => 1,
			]],
			'customer_email' => $user->email,
			'success_url' => $successUrl,
			'cancel_url'  => route('purchase.confirm', $item),
			'metadata'    => [
				'item_id'  => (string)$item->id,
				'buyer_id' => (string)$user->id,
			],
		]);

		// 一時住所情報は削除
		session()->forget("shipto.item_{$item->id}");

		// Stripeの決済画面に飛ばす
		return redirect()->away($checkout->url);
	}

	public function complete(Request $request)
	{
		$sessionId = $request->query('session_id');
		if (!$sessionId) {
			return redirect()->route('items.index')->with('error', 'セッションが見つかりません。');
		}

		Stripe::setApiKey(config('services.stripe.secret'));

		// Checkout Session を取得（検証：本当に paid か）
		$session = \Stripe\Checkout\Session::retrieve($sessionId);
		if (($session->payment_status ?? null) !== 'paid') {
			return redirect()->route('items.index')->with('error', '決済が完了していません。');
		}

    	// metadata から対象を特定
		$itemId  = (int)($session->metadata->item_id ?? 0);
		$buyerId = (int)($session->metadata->buyer_id ?? 0);

    	// 念のため本人確認
		if ($buyerId !== Auth::id()) {
			return redirect()->route('items.index')->with('error', '購入者情報が一致しません。');
		}

    	// ★ DBで重複登録を防ぎつつ、orders を作成して「売却済み」確定
		try {
			DB::transaction(function () use ($itemId, $buyerId) {
           		// 既に売却済みなら何もしない（冪等化（べきとうか）：同じ操作を繰り返しても結果が変わらない）
				if (Order::where('item_id', $itemId)->exists()) {
					return;
				}
				Order::create([
					'item_id'     => $itemId,
					'buyer_id'    => $buyerId,
                	// 必要なら 'status' => Order::STATUS_PAID など
				]);
			});
		} catch (\Throwable $e) {
        	// ユニーク制約違反などは黙って一覧へ（既に誰かが購入したケース）
			return redirect()->route('items.index')->with('error', 'すでに購入されています。');
		}

		return redirect()->route('items.index')->with('success', '購入が完了しました。ありがとうございました！');
	}
}
