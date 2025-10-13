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
				->when(Auth::check(), fn($q) => $q->where('user_id', '!=', Auth::id()))
				->latest()->paginate(12)->withQueryString();
		}

		return view('items.index', compact('items', 'tab', 'keyword'));
	}

	// 商品詳細
	public function detail(Item $item)
	{
		$item->load(['order'])->loadCount(['likes', 'comments']);
		return view('items.show', compact('item'));
	}
}
