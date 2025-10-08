<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use App\Http\Controllers\{
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

	// Stripe Webhook（CSRF除外）ルート
	Route::post('/stripe/webhook', [\App\Http\Controllers\StripeWebhookController::class, 'handleWebhook'])
		->withoutMiddleware([VerifyCsrfToken::class])
		->name('stripe.webhook');

	// 出品（表示→登録）
	Route::get('/sell',  [ExhibitionController::class, 'showSellForm'])->name('sell.show');
	Route::post('/sell', [ExhibitionController::class, 'productRegister'])->name('sell.perform');

	// マイページ（プロフィール表示／編集）
	Route::get('/mypage',           [UserController::class, 'profile'])->name('mypage.profile');
	Route::get('/mypage/profile',   [UserController::class, 'editForm'])->name('mypage.edit');
	Route::post('/mypage/profile',  [UserController::class, 'saveProfile'])->name('mypage.save');

	// コメント投稿（商品詳細内フォーム）
	Route::post('/item/{item}/comments', [CommentController::class, 'store'])->name('comments.store');

	// いいね（トグル：追加/解除）
	Route::post('/item/{item}/like',  [LikeController::class, 'like'])->name('like.add');
	Route::delete('/item/{item}/like', [LikeController::class, 'unlike'])->name('like.remove');

	// ※ /logout は Fortify が用意
});
