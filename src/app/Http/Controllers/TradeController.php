<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTradeMessageRequest;
use App\Http\Requests\UpdateTradeMessageRequest;
use App\Models\Trade;
use App\Models\TradeMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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
			'reviews',
			'messages' => function ($query) {
				$query->with('user')
					->orderBy('created_at');
			},
		])->findOrFail($tradeId);

		$this->authorizeTradeParticipant($trade, $userId);

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
			'trade' => $trade,
			'trades' => $trades,
			'userId' => $userId,
			'partnerName' => $partnerName,
		]);
	}

	public function storeMessage(StoreTradeMessageRequest $request, Trade $trade): RedirectResponse
	{
		$userId = Auth::id();

		$this->authorizeTradeParticipant($trade, $userId);

		$imagePath = null;

		if ($request->hasFile('image')) {
			$imagePath = $request->file('image')->store('trade_messages', 'public');
		}

		TradeMessage::create([
			'trade_id' => $trade->id,
			'user_id' => $userId,
			'body' => $request->input('body'),
			'image_path' => $imagePath,
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

	public function updateMessage(
		UpdateTradeMessageRequest $request,
		Trade $trade,
		TradeMessage $message
	): RedirectResponse {

		$userId = Auth::id();

		$this->authorizeTradeParticipant($trade, $userId);
		$this->ensureMessageBelongsToTrade($trade, $message);
		$this->authorizeMessageOwner($message, $userId);

		$updateData = [
			'body' => $request->input('body'),
		];

		if ($request->hasFile('image')) {
			if (!empty($message->image_path) && Storage::disk('public')->exists($message->image_path)) {
				Storage::disk('public')->delete($message->image_path);
			}

			$updateData['image_path'] = $request->file('image')->store('trade_messages', 'public');
		}

		$message->update($updateData);

		return redirect()->route('trades.show', $trade);
	}

	public function destroyMessage(Trade $trade, TradeMessage $message): RedirectResponse
	{
		$userId = Auth::id();

		$this->authorizeTradeParticipant($trade, $userId);
		$this->ensureMessageBelongsToTrade($trade, $message);
		$this->authorizeMessageOwner($message, $userId);

		if (!empty($message->image_path) && Storage::disk('public')->exists($message->image_path)) {
			Storage::disk('public')->delete($message->image_path);
		}

		$message->delete();

		return redirect()->route('trades.show', $trade);
	}

	private function ensureMessageBelongsToTrade(Trade $trade, TradeMessage $message): void
	{
		if ($message->trade_id !== $trade->id) {
			abort(404);
		}
	}

	private function authorizeMessageOwner(TradeMessage $message, int $userId): void
	{
		if ($message->user_id !== $userId) {
			abort(403);
		}
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

	private function authorizeTradeParticipant(Trade $trade, int $userId): void
	{
		if ($userId !== $trade->buyer_id && $userId !== $trade->seller_id) {
			abort(403);
		}
	}
}
