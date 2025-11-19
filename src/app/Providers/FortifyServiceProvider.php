<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use App\Actions\Fortify\CreateNewUser;

class FortifyServiceProvider extends ServiceProvider
{
	public function register(): void
	{
		Fortify::ignoreRoutes();
	}

	public function boot(): void
	{
		Fortify::loginView(fn() => view('auth.login'));
		Fortify::registerView(fn() => view('auth.register'));

		Fortify::createUsersUsing(CreateNewUser::class);

		Fortify::redirects('login', '/');
		Fortify::redirects('register', '/email/verify');
		Fortify::redirects('logout', '/login');
	}
}
