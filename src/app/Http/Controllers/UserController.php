<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Models\Item;
use App\Http\Requests\ProfileRequest;

class UserController extends Controller
{
	public function profile(Request $request): View
	{
		$user = $request->user();

		$tab = $request->query('page', 'sell');

		$sellingItems = $user->items()
			->with('order')
			->latest()
			->get();

		$purchasedItems = Item::with('order')
			->whereHas('order', fn($query) => $query->where('user_id', $user->id))
			->latest()
			->get();

		return view('mypage.profile', [
			'user'            => $user,
			'tab'             => $tab,
			'sellingItems'    => $sellingItems,
			'purchasedItems'  => $purchasedItems,
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

		$user->name        = $validated['name'];
		$user->postal_code = $validated['postal_code'] ?? null;
		$user->address     = $validated['address'] ?? null;
		$user->building    = $validated['building'] ?? null;

		if ($request->hasFile('avatar')) {
			$path = $request->file('avatar')->store('avatars', 'public');
			$user->avatar_path = $path;
		}

		$user->save();

		return redirect()
			->route('items.index');
	}
}
