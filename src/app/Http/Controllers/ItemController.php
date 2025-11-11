<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;

class ItemController extends Controller
{
	// 商品一覧（トップ）
	public function index(Request $request)
	{
		$tab = $request->string('tab', 'recommend')->toString();
		$keyword = $request->string('keyword')->toString();

		$base = Item::query()
			->with(['order'])
			->withCount(['likes', 'comments'])
			->when(
				$keyword,
				fn($q) =>
				$q->where('name', 'like', "%{$keyword}%")
			);

		if ($tab === 'mylist') {
			if (!Auth::check()) {
				return redirect()->route('login');
			}

			$items = $base
				->whereHas('likes', fn($q) => $q->where('user_id', Auth::id()))
				->latest()->paginate(12)->withQueryString();
		} else {
			$items = $base
				->latest()->paginate(12)->withQueryString();
		}

		return view('items.index', compact('items', 'tab', 'keyword'));
	}

	// 商品詳細
	public function detail(Item $item)
	{
		// 関連のまとめ取り（Eager Loading＝関連を一気に読む）
		$item->load(['user', 'categories', 'comments.user:id,name,avatar_path', 'likes', 'order'])
			->loadCount(['likes', 'comments']);

		// 画面制御用のフラグ（boolean＝真偽値）
		$isLiked = Auth::check()
			? $item->likes->contains('user_id', Auth::id())
			: false;

		$isSold = $item->order !== null;
		$isMine = Auth::id() === optional($item->user)->id; // 自分の出品かどうか

		return view('items.show', [
			'item'         => $item,
			'isLiked'      => $isLiked,
			'isSold'       => $isSold,
			'isMine'       => $isMine,
			'likeCount'    => $item->likes_count,     // loadCount の結果をそのまま渡す
			'commentCount' => $item->comments_count,  // 同上
		]);
	}
}
