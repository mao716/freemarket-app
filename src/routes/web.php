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

/*
|--------------------------------------------------------------------------
| 公開（認証不要）
|--------------------------------------------------------------------------
*/

Route::get('/', [ItemController::class, 'index'])->name('items.index');
Route::get('/item/{item}', [ItemController::class, 'detail'])->name('items.detail');

/*
|--------------------------------------------------------------------------
| ログイン・会員登録（未ログインのみ）
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
	Route::get('/login', [AuthLoginController::class, 'showLoginForm'])->name('login');
	Route::post('/login', [AuthLoginController::class, 'authenticate'])->name('login.perform');

	Route::get('/register', [AuthRegisterController::class, 'showRegisterForm'])->name('register');
	Route::post('/register', [AuthRegisterController::class, 'register'])->name('register.perform');
});

/*
|--------------------------------------------------------------------------
| メール認証関連
|--------------------------------------------------------------------------
*/

// 誘導画面（「メール確認してください」画面）
Route::get('/email/verify', function () {
	return view('auth.verify-email'); // blade は既にあるとのこと
})->middleware('auth')->name('verification.notice');

// メール内リンク（認証完了）
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
	$request->fulfill(); // email_verified_at をセット

	return redirect()->route('mypage.edit'); // 認証後：プロフィール設定へ
})->middleware(['auth'])->name('verification.verify');

// 認証メール再送
Route::post('/email/verification-notification', function (Request $request) {
	$request->user()->sendEmailVerificationNotification();

	return back()->with('status', 'verification-link-sent');
})->middleware(['auth'])->name('verification.send');

/*
|--------------------------------------------------------------------------
| 認証 & メール認証済みユーザーのみ
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {

	// 購入フロー
	Route::get('/purchase/{item}', [PurchaseController::class, 'confirm'])->name('purchase.confirm');
	Route::post('/purchase/{item}', [PurchaseController::class, 'store'])->name('purchase.store');
	Route::get('/purchase/complete', [PurchaseController::class, 'complete'])->name('purchase.complete');

	// 購入時の住所変更
	Route::get('/purchase/address/{item}', [AddressController::class, 'showAddressForm'])->name('address.show');
	Route::post('/purchase/address/{item}', [AddressController::class, 'saveAddress'])->name('address.save');

	// 出品
	Route::get('/sell', [ExhibitionController::class, 'showSellForm'])->name('sell.show');
	Route::post('/sell', [ExhibitionController::class, 'productRegister'])->name('sell.perform');

	// マイページ
	Route::get('/mypage', [UserController::class, 'profile'])->name('mypage.profile');
	Route::get('/mypage/profile', [UserController::class, 'editForm'])->name('mypage.edit');
	Route::post('/mypage/profile', [UserController::class, 'saveProfile'])->name('mypage.save');

	// コメント
	Route::post('/item/{item}/comments', [CommentController::class, 'store'])->name('comments.store');

	// いいね
	Route::post('/item/{item}/like', [LikeController::class, 'like'])->name('like.add');
	Route::delete('/item/{item}/like', [LikeController::class, 'unlike'])->name('like.remove');

	// ログアウト
	Route::post('/logout', [AuthLoginController::class, 'logout'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| Stripe Webhook（外部サービス用: CSRF除外）
|--------------------------------------------------------------------------
*/

Route::post(
	'/stripe/webhook',
	[StripeWebhookController::class, 'handleWebhook']
)->withoutMiddleware([VerifyCsrfToken::class])
	->name('stripe.webhook');
