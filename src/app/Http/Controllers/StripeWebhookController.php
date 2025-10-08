<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\Order;
use App\Models\User;
use App\Models\Item;
use Stripe\Webhook as StripeWebhook; // 署名検証（verify）用

class StripeWebhookController extends Controller
{
	public function handleWebhook(Request $request)
	{
		// 署名検証（verify：なりすまし防止）
		$payload = $request->getContent();
		$sigHeader = $request->header('Stripe-Signature');
		$secret = config('services.stripe.webhook_secret') ?? env('STRIPE_WEBHOOK_SECRET');

		try {
			$event = StripeWebhook::constructEvent($payload, $sigHeader, $secret);
		} catch (\Throwable $e) {
			Log::warning('Stripe webhook signature verify failed: ' . $e->getMessage());
			return response('invalid signature', 400);
		}

		// 冪等化（idempotency：同じイベントを二重処理しない）
		$eventId = $event['id'] ?? null;
		if ($eventId) {
			$cacheKey = 'stripe_event_' . $eventId;
			if (Cache::has($cacheKey)) {
				return response('duplicate', 200);
			}
			Cache::put($cacheKey, true, now()->addDay());
		}

		// 種別で分岐（checkout.session.completed = 決済完了）
		if ($event['type'] === 'checkout.session.completed') {
			$session = $event['data']['object'];

			// 1) ユーザー特定：Checkout作成時にcustomer_emailを入れておく想定
			$email = $session['customer_email'] ?? null;
			$user = $email ? User::where('email', $email)->first() : null;
			if (!$user) {
				Log::warning('Stripe webhook: user not found for email ' . $email);
				return response('user not found', 200); // 200で返す（再送ループ防止）
			}

			// 2) 商品IDは metadata に入れておく（後述のPurchaseController修正で送る）
			$itemId = $session['metadata']['item_id'] ?? null;
			$item   = $itemId ? Item::find($itemId) : null;
			if (!$item) {
				Log::warning('Stripe webhook: item not found for id ' . $itemId);
				return response('item not found', 200);
			}

			// 3) 支払い方法（card/konbini）
			$payment = ($session['payment_method_types'][0] ?? 'card');

			// 4) 住所スナップショット（snapshot：その時点の値を保存）
			$addressSnapshot = trim(sprintf(
				"〒 %s\n%s%s",
				$user->postal_code ?? '',
				$user->address ?? '',
				!empty($user->building) ? " {$user->building}" : ''
			));

			// 5) すでに同じユーザー×商品で注文があれば重複作成しない（簡易ガード）
			$exists = Order::where('user_id', $user->id)
				->where('item_id', $item->id)
				->exists();
			if ($exists) {
				return response('order exists', 200);
			}

			// 6) 注文を作成
			Order::create([
				'user_id' => $user->id,
				'item_id' => $item->id,
				'payment' => $payment,         // 'card' or 'konbini'
				'address' => $addressSnapshot, // プロフィールは変更せず、注文側に記録
				'status'  => \App\Models\Order::STATUS_PENDING, // 必要に応じて確定ステータスへ
			]);

			// ここで在庫更新や通知メールなどがあれば実行
			// $item->update(['is_sold' => true]); など
		}

		return response('ok', 200);
	}
}
