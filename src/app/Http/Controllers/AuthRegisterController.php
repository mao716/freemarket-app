<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest; // ← バリデーション（入力チェック）
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthRegisterController extends Controller
{
	// 画面表示（GET）
	public function showRegisterForm()
	{
		return view('auth.register'); // register.blade.php を返す（ビュー=画面テンプレート）
	}

	// 登録処理（POST）
	public function register(RegisterRequest $request)
	{
		$data = $request->validated();

		// ユーザー作成（password は必ず Hash::make で暗号化）
		$user = User::create([
			'name'        => $data['name'],
			'email'       => $data['email'],
			'password'    => Hash::make($data['password']),
		]);

		// 登録直後に自動ログイン
		Auth::login($user);

		return redirect()->route('mypage.edit');
	}
}
