<?php

namespace App\Http\Controllers;

use App\Models\Trade;
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
			'trade' => $trade->fresh([
				'buyer',
				'seller',
				'order.item',
				'messages.user',
				'reviews',
			]),
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
}
