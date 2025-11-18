<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
	public function showAddressForm(Item $item)
	{
		$user = Auth::user();
		$sessionKey = "shipto.item_{$item->id}";
		$addressData = session($sessionKey) ?? [
			'postal_code' => $user->postal_code,
			'address'     => $user->address,
			'building'    => $user->building,
		];

		return view('purchase.address_edit', compact('item', 'addressData'));
	}

	public function saveAddress(AddressRequest $request, Item $item)
	{
		$sessionKey = "shipto.item_{$item->id}";
		session([$sessionKey => $request->only('postal_code', 'address', 'building')]);

		return redirect()->route('purchase.confirm', $item);
	}
}
