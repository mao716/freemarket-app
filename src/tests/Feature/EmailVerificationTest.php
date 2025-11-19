<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;

class EmailVerificationTest extends TestCase
{
	use RefreshDatabase;

	public function test_会員登録後に認証メールが送信される()
	{
		Notification::fake();

		$response = $this->post(route('register.perform'), [
			'name'                  => 'テストユーザー',
			'email'                 => 'test@example.com',
			'password'              => 'password123',
			'password_confirmation' => 'password123',
		]);

		$response->assertRedirect(route('verification.notice'));

		$user = User::first();
		$this->assertNotNull($user);

		Notification::assertSentTo($user, VerifyEmail::class);
	}

	public function test_未認証ユーザーはメール認証誘導画面に遷移しボタンが表示される()
	{
		$user = User::factory()->create([
			'email_verified_at' => null,
		]);

		$response = $this->actingAs($user)->get(route('verification.notice'));

		$response->assertStatus(200);
		$response->assertSee('登録していただいたメールアドレスに認証メールを送付しました');
		$response->assertSee('認証はこちらから');
	}

	public function test_認証URLアクセスでメール認証されプロフィール設定にリダイレクトされる()
	{
		$user = User::factory()->create([
			'email_verified_at' => null,
		]);

		$url = route('verification.verify', [
			'id'   => $user->id,
			'hash' => sha1($user->email),
		]);

		$response = $this->actingAs($user)->get($url);

		$response->assertRedirect(route('mypage.edit'));

		$this->assertNotNull($user->fresh()->email_verified_at);
	}
}
