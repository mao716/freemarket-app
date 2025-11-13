<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * 1) 会員登録機能
 * 用語メモ：バリデーション（入力の正しさチェック）、リダイレクト（自動遷移）
 */
class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 名前未入力ならエラーになる()
    {
        $response = $this->post('/register', [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['name']); // セッション（一時保存領域）にエラーが入るか
    }

    /** @test */
    public function 全項目OKなら登録されてプロフィール設定画面へ遷移する()
    {
        $response = $this->post('/register', [
            'name' => 'まおちゃん',
            'email' => 'mao@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertDatabaseHas('users', ['email' => 'mao@example.com']);
        // 基本設計に合わせて遷移先を調整（例：/mypage/profile）
        $response->assertRedirect('/mypage/profile');
    }
}
