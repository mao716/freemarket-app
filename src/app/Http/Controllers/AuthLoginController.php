<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuthLoginController extends Controller
{
	public function showLoginForm()
	{
		return view('auth.login');
	}

	public function authenticate(LoginRequest $request)
	{
		$credentials = $request->validated();

		if (Auth::attempt($credentials)) {
			$request->session()->regenerate();

			return redirect()->intended('/');
		}

		return back()
			->withErrors(['email' => 'ログイン情報が登録されていません'])
			->onlyInput('email');
	}

	public function logout(Request $request)
	{
		Auth::logout();
		$request->session()->invalidate();
		$request->session()->regenerateToken();

		return redirect('/login');
	}
}
