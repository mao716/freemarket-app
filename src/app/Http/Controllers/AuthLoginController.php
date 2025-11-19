<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthLoginController extends Controller
{
	public function showLoginForm()
	{
		return view('auth.login');
	}

	public function authenticate(Request $request)
	{
		$loginRequest = new LoginRequest();

		Validator::make(
			$request->all(),
			$loginRequest->rules(),
			$loginRequest->messages()
		)->validate();

		$credentials = $request->only('email', 'password');

		if (Auth::attempt($credentials)) {
			$request->session()->regenerate();

			return redirect()->intended(route('items.index'));
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
