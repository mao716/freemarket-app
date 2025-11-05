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
		// DBトランザクション（transaction＝一連の処理をまとめて失敗時に戻せるようにする）
		DB::transaction(function () use ($request) {
			// 画像をstorage/app/public/items に保存
			// store()は自動でユニークなファイル名を生成してくれる
			$imagePath = $request->file('image')->store('items', 'public');

			// 商品を登録（create＝新しいレコードを作成）
			$item = Item::create([
				'user_id'     => Auth::id(),
				'name'        => $request->input('name'),
				'brand'       => $request->input('brand'),
				'description' => $request->input('description'),
				'price'       => $request->input('price'),
				'condition'   => $request->input('condition'),
				'image_path'  => $imagePath,
			]);

			// カテゴリの紐付け（多対多＝belongsToMany）
			$item->categories()->sync($request->input('categories'));
		});

		// 登録完了後、一覧へリダイレクト（redirect＝ページ移動）
		return redirect()
			->route('items.index');
	}
}
