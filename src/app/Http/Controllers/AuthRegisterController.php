<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthRegisterController extends Controller
{
	public function showRegisterForm()
	{
		return view('auth.register');
	}

	public function register(RegisterRequest $request)
	{
		$data = $request->validated();

		$user = User::create([
			'name'        => $data['name'],
			'email'       => $data['email'],
			'password'    => Hash::make($data['password']),
		]);

		Auth::login($user);

		$user->sendEmailVerificationNotification();

		return redirect()->route('verification.notice');
	}
}
