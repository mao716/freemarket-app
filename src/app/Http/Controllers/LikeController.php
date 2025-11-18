<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Like;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
	public function like(Item $item)
	{
		$userId = Auth::id();

		Like::firstOrCreate([
			'item_id' => $item->id,
			'user_id' => $userId,
		]);

		return back();
	}

	public function unlike(Item $item)
	{
		$userId = Auth::id();

		Like::where('item_id', $item->id)
			->where('user_id', $userId)
			->delete();

		return back();
	}
}
