<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTradeMessageRequest;
use App\Models\Trade;
use App\Models\TradeMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TradeController extends Controller
{
	public function show(int $tradeId): View
	{
		$userId = Auth::id();

		$trade = Trade::with([
			'buyer',
			'seller',
			'order.item',
			'messages.user',
			'reviews',
		])->findOrFail($tradeId);

		if ($userId !== $trade->buyer_id && $userId !== $trade->seller_id) {
			abort(403);
		}

		$this->resetUnreadCount($trade, $userId);

		$trade->load([
			'buyer',
			'seller',
			'order.item',
			'messages.user',
			'reviews',
		]);

		$trades = Trade::with(['order.item'])
			->where(function ($query) use ($userId) {
				$query->where('buyer_id', $userId)
					->orWhere('seller_id', $userId);
			})
			->orderByDesc('last_message_at')
			->orderByDesc('updated_at')
			->get();

		$partnerName = $userId === $trade->buyer_id
			? $trade->seller->name
			: $trade->buyer->name;

		return view('trades.show', [
			'trade' => $trade,
			'trades' => $trades,
			'userId' => $userId,
			'partnerName' => $partnerName,
		]);
	}

	private function resetUnreadCount(Trade $trade, int $userId): void
	{
		if ($userId === $trade->buyer_id && $trade->buyer_unread_count > 0) {
			$trade->update([
				'buyer_unread_count' => 0,
			]);

			return;
		}

		if ($userId === $trade->seller_id && $trade->seller_unread_count > 0) {
			$trade->update([
				'seller_unread_count' => 0,
			]);
		}
	}

	public function storeMessage(StoreTradeMessageRequest $request, Trade $trade)
	{
		$userId = Auth::id();

		if ($userId !== $trade->buyer_id && $userId !== $trade->seller_id) {
			abort(403);
		}

		TradeMessage::create([
			'trade_id' => $trade->id,
			'user_id' => $userId,
			'body' => $request->input('body'),
		]);

		$updateData = [
			'last_message_at' => now(),
		];

		if ($userId === $trade->buyer_id) {
			$updateData['seller_unread_count'] = $trade->seller_unread_count + 1;
		} else {
			$updateData['buyer_unread_count'] = $trade->buyer_unread_count + 1;
		}

		$trade->update($updateData);

		return redirect()->route('trades.show', $trade);
	}
}
