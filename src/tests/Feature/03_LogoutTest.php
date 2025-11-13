<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * 3) ログアウト機能
 * 用語メモ：POSTメソッド（データ送信）、CSRFトークン（改ざん防止）
 */
class LogoutTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ログアウトできる()
    {
        $user = User::factory()->create();
        $this->actingAs($user); // 擬似ログイン（テスト用のログイン）

        $response = $this->post('/logout');
        $response->assertRedirect('/'); // ヘッダーのボタンから正常にログアウト
        $this->assertGuest(); // 非ログイン状態（ゲスト）
    }
}
