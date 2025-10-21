<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseRequest;
use App\Models\Item;
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

		// Stripe APIキー設定（.envのSTRIPE_SECRETを使用）
		Stripe::setApiKey(config('services.stripe.secret'));

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
			'success_url' => route('items.index'), // Stripe完了画面のあと戻る先
			'cancel_url' => route('purchase.confirm', $item),
		]);

		// 一時住所情報は削除
		session()->forget("shipto.item_{$item->id}");

		// Stripeの決済画面に飛ばす
		return redirect()->away($checkout->url);
	}
}
