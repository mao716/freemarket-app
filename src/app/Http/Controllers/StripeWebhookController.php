<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\Order;
use App\Models\User;
use App\Models\Item;
use Stripe\Webhook as StripeWebhook;

class StripeWebhookController extends Controller
{
	public function handleWebhook(Request $request)
	{
		$payload = $request->getContent();
		$sigHeader = $request->header('Stripe-Signature');
		$secret = config('services.stripe.webhook_secret') ?? env('STRIPE_WEBHOOK_SECRET');

		try {
			$event = StripeWebhook::constructEvent($payload, $sigHeader, $secret);
		} catch (\Throwable $e) {
			Log::warning('Stripe webhook signature verify failed: ' . $e->getMessage());
			return response('invalid signature', 400);
		}

		$eventId = $event['id'] ?? null;
		if ($eventId) {
			$cacheKey = 'stripe_event_' . $eventId;
			if (Cache::has($cacheKey)) {
				return response('duplicate', 200);
			}
			Cache::put($cacheKey, true, now()->addDay());
		}

		if ($event['type'] === 'checkout.session.completed') {
			$session = $event['data']['object'];
			$email = $session['customer_email'] ?? null;
			$user = $email ? User::where('email', $email)->first() : null;

			if (!$user) {
				Log::warning('Stripe webhook: user not found for email ' . $email);
				return response('user not found', 200);
			}

			$itemId = $session['metadata']['item_id'] ?? null;
			$item   = $itemId ? Item::find($itemId) : null;

			if (!$item) {
				Log::warning('Stripe webhook: item not found for id ' . $itemId);
				return response('item not found', 200);
			}

			$payment = $session['metadata']['payment'] ?? Order::PAYMENT_CARD;
			$addressSnapshot = trim(sprintf(
				"〒 %s\n%s%s",
				$user->postal_code ?? '',
				$user->address ?? '',
				!empty($user->building) ? " {$user->building}" : ''
			));

			$exists = Order::where('user_id', $user->id)
				->where('item_id', $item->id)
				->exists();
			if ($exists) {
				return response('order exists', 200);
			}

			Order::create([
				'user_id' => $user->id,
				'item_id' => $item->id,
				'payment' => $payment,
				'address' => $addressSnapshot,
				'status'  => \App\Models\Order::STATUS_PENDING,
			]);

		}

		return response('ok', 200);
	}
}
