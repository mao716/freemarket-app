<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Models\Item;
use App\Http\Requests\ProfileRequest; // フォームリクエスト（バリデーション専用クラス）

class UserController extends Controller
{
	/** マイページのダッシュボード（お好みで） */
	public function profile(Request $request): View
	{
		$user = $request->user();

		// ★初期タブは 'sell'（= 出品した商品）
		$tab = $request->query('page', 'sell');

		// 出品した商品（自分が出した Item 一覧）
		// ※ order（売却情報）も一緒に取得しておくと SOLD 判定に使える
		$sellingItems = $user->items()
			->with('order')          // ← hasOne(order) 前提
			->latest()
			->get();

		// 購入した商品（自分が購入した Item 一覧）
		$purchasedItems = Item::with('order')
			->whereHas('order', fn($q) => $q->where('user_id', $user->id))
			->latest()
			->get();

		return view('mypage.profile', [
			'user'            => $user,
			'tab'             => $tab,           // ★Blade にも渡す
			'sellingItems'    => $sellingItems,
			'purchasedItems'  => $purchasedItems,
		]);
	}

	/** プロフィール編集の表示（初回／通常共通） */
	public function editForm(Request $request): View
	{
		$user = $request->user();

		// 初回かどうかの判定（郵便番号 or 住所が空なら初回扱い）
		$isFirstSetup = empty($user->postal_code) || empty($user->address);

		return view('mypage.profile_edit', [
			'user' => $user,
			'isFirstSetup' => $isFirstSetup,
		]);
	}

	/** プロフィールの更新 */
	public function saveProfile(ProfileRequest $request): RedirectResponse
	{
		$user = $request->user();

		// テキスト系の反映
		$user->name        = $request->input('name');
		$user->postal_code = $request->input('postal_code');
		$user->address     = $request->input('address');
		$user->building    = $request->input('building');

		// 画像（avatar）の保存処理（multipart必須：ファイル送信用のエンコード形式）
		if ($request->hasFile('avatar')) {
			// publicディスク配下に保存（storage/app/public/avatars）
			$path = $request->file('avatar')->store('avatars', 'public');

			// Webから参照しやすいように 'storage/...' で保存（asset('storage/...') で参照可）
			$user->avatar_path = 'storage/' . $path;
		}

		$user->save();

		return redirect()
			->route('items.index');
	}
}
