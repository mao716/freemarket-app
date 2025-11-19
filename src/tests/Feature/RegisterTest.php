<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_名前未入力ならエラーになる()
    {
        $response = $this->post('/register', [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_全項目OKなら登録されてプロフィール設定画面へ遷移する()
    {
        $response = $this->post('/register', [
            'name' => 'まお',
            'email' => 'mao@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertDatabaseHas('users', ['email' => 'mao@example.com']);
        $response->assertRedirect('/mypage/profile');
    }
}
