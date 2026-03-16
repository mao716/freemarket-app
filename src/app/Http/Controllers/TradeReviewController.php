<?php

namespace App\Http\Controllers;

use App\Models\Trade;
use App\Models\TradeReview;
use App\Mail\TradeCompletedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class TradeReviewController extends Controller
{
	public function store(Request $request, Trade $trade): RedirectResponse
	{
		$userId = Auth::id();

		$trade->load(['seller', 'order.item']);

		if ($userId !== $trade->buyer_id && $userId !== $trade->seller_id) {
			abort(403);
		}

		$request->validate([
			'rating' => ['required', 'integer', 'between:1,5'],
		], [
			'rating.required' => '評価を選択してください',
			'rating.integer' => '評価を正しく入力してください',
			'rating.between' => '評価は1〜5で選択してください',
		]);

		$alreadyReviewed = TradeReview::where('trade_id', $trade->id)
			->where('reviewer_id', $userId)
			->exists();

		if ($alreadyReviewed) {
			return redirect()
				->route('trades.show', $trade)
				->with('error', 'この取引はすでに評価済みです');
		}

		$revieweeId = $userId === $trade->buyer_id
			? $trade->seller_id
			: $trade->buyer_id;

		TradeReview::create([
			'trade_id' => $trade->id,
			'reviewer_id' => $userId,
			'reviewee_id' => $revieweeId,
			'rating' => $request->input('rating'),
		]);

		if ($userId === $trade->buyer_id) {
			$trade->update([
				'status' => $trade->status === 2 ? 3 : 1,
			]);

			Mail::to($trade->seller->email)->send(new TradeCompletedNotification($trade));

			return redirect()->route('items.index');
		}

		$trade->update([
			'status' => $trade->status === 1 ? 3 : 2,
		]);

		return redirect()->route('items.index');
	}
}
