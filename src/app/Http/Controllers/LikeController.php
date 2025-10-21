<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Like;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
	// いいね追加
	public function like(Item $item)
	{
		// すでにいいね済みなら何もしない
		$exists = Like::where('item_id', $item->id)
			->where('user_id', Auth::id())
			->exists();
		if ($exists) return back();

		Like::create([
			'item_id' => $item->id,
			'user_id' => Auth::id(),
		]);

		return back();
	}

	// いいね解除
	public function unlike(Item $item)
	{
		Like::where('item_id', $item->id)
			->where('user_id', Auth::id())
			->delete();

		return back();
	}
}
