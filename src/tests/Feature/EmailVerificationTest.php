<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
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
			'name' => 'テストユーザー',
			'email' => 'test@example.com',
			'password' => 'password123',
			'password_confirmation' => 'password123',
		]);

		$response->assertRedirect(route('verification.notice'));

		$user = User::first();
		$this->assertNotNull($user);

		Notification::assertSentTo($user, VerifyEmail::class);
	}

	public function test_誘導画面に認証はこちらからボタンが表示される()
	{
		$user = User::factory()->create([
			'email_verified_at' => null,
		]);

		$response = $this->actingAs($user)->get(route('verification.notice'));

		$response->assertStatus(200);
		$response->assertSee('認証はこちらから');
	}

	public function test_認証URLアクセスで認証されプロフィール設定画面に遷移する()
	{
		Notification::fake();

		$user = User::factory()->create([
			'email_verified_at' => null,
		]);

		$this->actingAs($user)->post(route('verification.send'));

		Notification::assertSentTo(
			$user,
			VerifyEmail::class,
			function (VerifyEmail $notification) use ($user) {
				$verificationUrl = $notification->toMail($user)->actionUrl;
				$response = $this->actingAs($user)->get($verificationUrl);
				$response->assertRedirect(route('mypage.edit'));
				$this->assertNotNull($user->fresh()->email_verified_at);
				return true;
			}
		);
	}
}
