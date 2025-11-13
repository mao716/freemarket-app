<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * 13) ユーザー情報取得（プロフィール表示）
 * 用語メモ：関連（リレーション：モデル同士のつながり）
 */
class ProfileShowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function プロフィール画面で必要な情報が表示される()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->get('/mypage')
            ->assertStatus(200)
            ->assertSee($user->name);
    }
}
