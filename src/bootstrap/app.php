<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
	->withRouting(
		web: __DIR__ . '/../routes/web.php',
		commands: __DIR__ . '/../routes/console.php',
		health: '/up',
	)
	->withMiddleware(function (Middleware $middleware) {
		// webグループを定義（順番が大事：Cookie→Session→CSRF→Binding）
		$middleware->group('web', [
			\Illuminate\Cookie\Middleware\EncryptCookies::class,
			\Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
			\Illuminate\Session\Middleware\StartSession::class,
			\Illuminate\View\Middleware\ShareErrorsFromSession::class,
			\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
			\Illuminate\Routing\Middleware\SubstituteBindings::class,
		]);

		// （任意）apiグループも一応定義
		$middleware->group('api', [
			\Illuminate\Routing\Middleware\SubstituteBindings::class,
		]);
	})
	->withExceptions(function (Exceptions $exceptions): void {
		//
	})
	->withProviders([
		App\Providers\FortifyServiceProvider::class,
	])
	->create();
