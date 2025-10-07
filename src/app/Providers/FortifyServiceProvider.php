<?php

namespace App\Providers;

use Laravel\Fortify\Fortify;
use Illuminate\Support\ServiceProvider;

class FortifyServiceProvider extends ServiceProvider
{
	public function register(): void {}

	public function boot(): void
	{
		// 画面（ビュー）紐づけ
		Fortify::loginView(fn() => view('auth.login'));
		Fortify::registerView(fn() => view('auth.register'));

		// 登録処理クラス
		Fortify::createUsersUsing(\App\Actions\Fortify\CreateNewUser::class);

		// ログイン認証はデフォルトに任せる

		// 登録直後はプロフィール設定へ（メール認証を使わない場合）
		Fortify::redirects('register', '/mypage/profile');

		// （応用：メール認証を使う時は verifyEmailView と features を別途設定）
		// Fortify::verifyEmailView(fn () => view('auth.verify-email'));
	}
}
