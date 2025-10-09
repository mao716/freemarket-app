<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
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


// =================== 認証不要（誰でも閲覧OK） ===================

// トップ（商品一覧）: /
// ※ ?tab=mylist が来たらコントローラ側で分岐（サーバ側ロジックで出し分け）
Route::get('/', [ItemController::class, 'index'])->name('items.index');

// 商品詳細: /item/{item}
Route::get('/item/{item}', [ItemController::class, 'detail'])->name('items.detail');


// =================== 認証：ログイン/ログアウト（自前実装） ＆ 会員登録 ===================

// guest（= 未ログインの人だけ入れる）
Route::middleware('guest')->group(function () {
	// ログイン画面（GET）
	Route::get('/login', [AuthLoginController::class, 'showLoginForm'])->name('login.show');
	// ログイン処理（POST） throttle（= 一定時間内の回数制限）で総当たり対策
	Route::post('/login', [AuthLoginController::class, 'authenticate'])
		->middleware('throttle:6,1')
		->name('login');

	// 会員登録画面表示（GET）
	Route::get('/register', [AuthRegisterController::class, 'showRegisterForm'])
		->name('register');
	// 登録（POST = フォーム送信）
	Route::post('/register', [AuthRegisterController::class, 'register'])
		->middleware('throttle:6,1') // （throttle=一定時間の回数制限）
		->name('register.perform');
});

// auth（= ログイン済の人だけ入れる）
Route::middleware('auth')->group(function () {
	// ログアウト処理（POST）
	Route::post('/logout', [AuthLoginController::class, 'logout'])->name('logout');
});


// =================== 認証必須（ログインが必要） ===================

// ★ローカル開発だけ“自動ログイン→auth”の順で適用
$protected = app()->isLocal() && config('dev.autologin')
	? ['dev.autologin', 'auth']
	: ['auth'];

Route::middleware($protected)->group(function () {
	// 購入フロー（確認→購入実行）
	Route::get('/purchase/{item}',  [PurchaseController::class, 'confirm'])->name('purchase.confirm');
	Route::post('/purchase/{item}', [PurchaseController::class, 'purchase'])->name('purchase.perform');

	// 住所変更（購入画面から遷移）
	Route::get('/purchase/address/{item}',  [AddressController::class, 'showAddressForm'])->name('address.show');
	Route::post('/purchase/address/{item}', [AddressController::class, 'saveAddress'])->name('address.save');

	// 出品（表示→登録）
	Route::get('/sell',  [ExhibitionController::class, 'showSellForm'])->name('sell.show');
	Route::post('/sell', [ExhibitionController::class, 'productRegister'])->name('sell.perform');

	// マイページ（プロフィール表示／編集）
	Route::get('/mypage',  [UserController::class, 'profile'])->name('mypage.profile');
	Route::get('/mypage/profile', [UserController::class, 'editForm'])->name('mypage.edit');
	Route::post('/mypage/profile',  [UserController::class, 'saveProfile'])->name('mypage.save');

	// コメント投稿（商品詳細内フォーム）
	Route::post('/item/{item}/comments', [CommentController::class, 'store'])->name('comments.store');

	// いいね（トグル：追加/解除）
	Route::post('/item/{item}/like',  [LikeController::class, 'like'])->name('like.add');
	Route::delete('/item/{item}/like', [LikeController::class, 'unlike'])->name('like.remove');
});

// Stripe Webhook（CSRF除外）ルート
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])
	->withoutMiddleware([VerifyCsrfToken::class])
	->name('stripe.webhook');
