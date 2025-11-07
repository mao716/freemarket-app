<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExhibitionRequest;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ExhibitionController extends Controller
{
	/**
	 * 出品画面の表示
	 */
	public function showSellForm(): View
	{
		// カテゴリ一覧を取得してビューに渡す
		$categories = Category::orderBy('name')->get();

		return view('items.create', compact('categories'));
	}

	/**
	 * 出品内容の登録
	 */
	public function productRegister(ExhibitionRequest $request): RedirectResponse
	{
		DB::transaction(function () use ($request) {
			// 1) 画像を storage/app/public/items に保存（store＝保存＆パスを返す）
			$storedPath = $request->file('image')->store('items', 'public'); // 例: "items/vaTaNf...k.jpg"

			$imagePath = $storedPath;

			// 3) 商品を登録
			$item = Item::create([
				'user_id'     => Auth::id(),
				'name'        => $request->input('name'),
				'brand'       => $request->input('brand'),
				'description' => $request->input('description'),
				'price'       => $request->input('price'),
				'condition'   => $request->input('condition'),
				'image_path'  => $imagePath, // ★ここをファイル名だけに
			]);

			$item->categories()->sync($request->input('categories'));
		});

		return redirect()->route('items.index');
	}
}
