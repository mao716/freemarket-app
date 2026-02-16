<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;

class ItemController extends Controller
{
	public function index(Request $request)
	{
		$tab = $request->string('tab', 'recommend')->toString();
		$keyword = $request->string('keyword')->toString();
		$userId = Auth::id();

		$itemQuery = Item::query()
			->with(['order'])
			->withCount(['likes', 'comments'])
			->when(
				$keyword,
				fn($query) =>
				$query->where('name', 'like', "%{$keyword}%")
			);

		if ($tab === 'mylist') {
			if (!$userId) {
				return view('items.index', [
					'items' => collect(),
					'tab' => $tab,
					'keyword' => $keyword,
				]);
			}

			$items = $itemQuery
				->whereHas('likes', fn($query) => $query->where('user_id', $userId))
				->where('items.user_id', '!=', $userId)
				->latest()
				->get();

			return view('items.index', compact('items', 'tab', 'keyword'));
		}

		$items = $itemQuery
			->when($userId, fn($query) => $query->where('items.user_id', '!=', $userId))
			->latest()
			->get();

		return view('items.index', compact('items', 'tab', 'keyword'));
	}

	public function detail(Item $item)
	{
		$item->load(['user', 'categories', 'comments.user:id,name,avatar_path', 'likes', 'order'])
			->loadCount(['likes', 'comments']);

		$isLiked = Auth::check()
			? $item->likes->contains('user_id', Auth::id())
			: false;

		$isSold = $item->order !== null;
		$isMine = Auth::id() === optional($item->user)->id;

		return view('items.show', [
			'item'         => $item,
			'isLiked'      => $isLiked,
			'isSold'       => $isSold,
			'isMine'       => $isMine,
			'likeCount'    => $item->likes_count,
			'commentCount' => $item->comments_count,
		]);
	}
}
