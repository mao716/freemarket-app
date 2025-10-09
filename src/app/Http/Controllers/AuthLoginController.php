<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;

class AuthLoginController extends Controller
{
	public function showLoginForm()
	{
		return view('auth.login');
	}

	public function authenticate(LoginRequest $request)
	{
		// ↓ バリデーション済データ（=安全に検証済みの入力）
		$credentials = $request->validated();

		// ここでログインを試みる（rememberは今回は未対応）
		if (Auth::attempt($credentials)) {
			// 乗っ取り対策（セッションID再発行）
			$request->session()->regenerate();

			// "/"（トップ）へ
			return redirect()->intended('/');
		}

		// ログイン失敗時：文言は要件どおり・メールのみ保持
		return back()
			->withErrors(['email' => 'ログイン情報が登録されていません'])
			->onlyInput('email');
	}

	public function logout()
	{
		Auth::logout();
		request()->session()->invalidate();
		request()->session()->regenerateToken();
		return redirect('/'); // トップへ
	}
}
