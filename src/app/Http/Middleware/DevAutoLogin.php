<?php
// app/Http/Middleware/DevAutoLogin.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DevAutoLogin
{
	private const DEFAULT_USER_ID = 1; // 予備（.env優先）

	public function handle(Request $request, Closure $next)
	{
		// 1) フラグOFFなら何もしない
		if (! config('dev.autologin')) {
			return $next($request);
		}

		// 2) ローカル以外（本番/採点用PCなど）では動かさない保険
		if (! app()->isLocal()) {
			return $next($request);
		}

		// 3) すでにログイン済みならスルー
		if (auth()->check()) {
			return $next($request);
		}

		// 4) 指定ユーザーで自動ログイン
		$userId = config('dev.autologin_user_id') ?: self::DEFAULT_USER_ID;
		auth()->loginUsingId($userId);

		return $next($request);
	}
}
