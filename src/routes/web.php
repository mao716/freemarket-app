<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

use App\Http\Controllers\{
	AuthLoginController,
	AuthRegisterController,
	AddressController,
	CommentController,
	ExhibitionController,
	ItemController,
	LikeController,
	PurchaseController,
	UserController,
	StripeWebhookController,
};

// =================== 認証不要 ===================

Route::get('/', [ItemController::class, 'index'])
	->name('items.index');

Route::get('/item/{item}', [ItemController::class, 'detail'])
	->name('items.detail');

// =================== 認証：ログイン / 会員登録 ===================

Route::middleware('guest')->group(function () {
	// ログイン画面
	Route::get('/login', [AuthLoginController::class, 'showLoginForm'])
		->name('login');

	// ログイン処理
	Route::post('/login', [AuthLoginController::class, 'authenticate'])
		->name('login.perform');

	// 会員登録画面
	Route::get('/register', [AuthRegisterController::class, 'showRegisterForm'])
		->name('register');

	// 会員登録処理
	Route::post('/register', [AuthRegisterController::class, 'register'])
		->name('register.perform');
});

// =================== メール認証関連 ===================

// 認証案内ページ
Route::get('/email/verify', function () {
	return view('auth.verify-email');
})->middleware('auth')
	->name('verification.notice');

// メール内リンクからの本登録
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
	$request->fulfill(); // email_verified_at を埋める
	return redirect()->route('mypage.edit'); // プロフィール設定画面へ
})->middleware(['auth'])
	->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
	$request->user()->sendEmailVerificationNotification();
	return back()->with('status', 'verification-link-sent');
})->middleware(['auth'])
	->name('verification.send');

Route::middleware(['auth', 'verified'])->group(function () {

	Route::get('/purchase/{item}', [PurchaseController::class, 'confirm'])
		->name('purchase.confirm');
	Route::get('/purchase/complete', [PurchaseController::class, 'complete'])
		->name('purchase.complete');
	Route::post('/purchase/{item}', [PurchaseController::class, 'store'])
		->name('purchase.store');

	Route::get('/purchase/address/{item}', [AddressController::class, 'showAddressForm'])
		->name('address.show');
	Route::post('/purchase/address/{item}', [AddressController::class, 'saveAddress'])
		->name('address.save');

	Route::get('/sell', [ExhibitionController::class, 'showSellForm'])
		->name('sell.show');
	Route::post('/sell', [ExhibitionController::class, 'productRegister'])
		->name('sell.perform');

	Route::get('/mypage', [UserController::class, 'profile'])
		->name('mypage.profile');
	Route::get('/mypage/profile', [UserController::class, 'editForm'])
		->name('mypage.edit');
	Route::post('/mypage/profile', [UserController::class, 'saveProfile'])
		->name('mypage.save');

	Route::post('/item/{item}/comments', [CommentController::class, 'store'])
		->name('comments.store');

	Route::post('/item/{item}/like', [LikeController::class, 'like'])
		->name('like.add');
	Route::delete('/item/{item}/like', [LikeController::class, 'unlike'])
		->name('like.remove');

	Route::post('/logout', [AuthLoginController::class, 'logout'])
		->name('logout');
});

// =================== Stripe Webhook ===================

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])
	->withoutMiddleware([VerifyCsrfToken::class])
	->name('stripe.webhook');
