<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use App\Models\Item;

class AddressController extends Controller
{
	public function show(Item $item)
	{
		$user = auth()->user();

		// すでに一時保存があればそれを初期値に、無ければプロフィール初期値
		$sessionKey = "shipto.item_{$item->id}";
		$seed = session($sessionKey) ?? [
			'postal_code' => $user->postal_code,
			'address'     => $user->address,
			'building'    => $user->building,
		];

		return view('purchase.address_edit', compact('item', 'seed'));
	}

	public function update(AddressRequest $request, Item $item) // 送付先をセッションへ
	{
		$sessionKey = "shipto.item_{$item->id}";
		session([$sessionKey => $request->only('postal_code', 'address', 'building')]);

		// 購入画面へ戻す
		return redirect()->route('purchase.confirm', $item)
			->with('success', '送付先住所を更新しました');
	}
}
