<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Models\Item;
use App\Models\Trade;
use App\Http\Requests\ProfileRequest;

class UserController extends Controller
{
	public function profile(Request $request): View
	{
		$user = $request->user();

		$tab = $request->query('page', 'sell');

		$tradingItems = Trade::with(['order.item'])
			->where(function ($query) use ($user) {
				$query->where('buyer_id', $user->id)
					->orWhere('seller_id', $user->id);
			})
			->whereIn('status', [0, 1, 2])
			->orderByDesc('last_message_at')
			->orderByDesc('updated_at')
			->get();

		$tradingItemIds = $tradingItems
			->pluck('order.item.id')
			->filter()
			->unique()
			->values();

		$sellingItems = $user->items()
			->with('order')
			->whereNotIn('id', $tradingItemIds)
			->latest()
			->get();

		$purchasedItems = Item::with('order')
			->whereHas('order', fn($query) => $query->where('user_id', $user->id))
			->whereNotIn('id', $tradingItemIds)
			->latest()
			->get();

		$tradeUnreadCount = $tradingItems->sum(function ($trade) use ($user) {
			return $user->id === $trade->buyer_id
				? $trade->buyer_unread_count
				: $trade->seller_unread_count;
		});

		return view('mypage.profile', [
			'user' => $user,
			'tab' => $tab,
			'sellingItems' => $sellingItems,
			'purchasedItems' => $purchasedItems,
			'tradingItems' => $tradingItems,
			'tradeUnreadCount' => $tradeUnreadCount,
		]);
	}

	public function editForm(Request $request): View
	{
		$user = $request->user();

		$isFirstSetup = empty($user->postal_code) || empty($user->address);

		return view('mypage.profile_edit', [
			'user' => $user,
			'isFirstSetup' => $isFirstSetup,
		]);
	}

	public function saveProfile(ProfileRequest $request): RedirectResponse
	{
		$user = $request->user();

		$validated = $request->validated();

		$user->name = $validated['name'];
		$user->postal_code = $validated['postal_code'] ?? null;
		$user->address = $validated['address'] ?? null;
		$user->building = $validated['building'] ?? null;

		if ($request->hasFile('avatar')) {
			$path = $request->file('avatar')->store('avatars', 'public');
			$user->avatar_path = $path;
		}

		$user->save();

		return redirect()->route('items.index');
	}
}
