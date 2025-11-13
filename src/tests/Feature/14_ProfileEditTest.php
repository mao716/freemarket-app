<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * 14) ユーザー情報変更（プロフィール編集）
 * 用語メモ：ストレージリンク（storageとpublicの連携）、マスアサイン（配列一括代入）
 */
class ProfileEditTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function プロフィールを更新できる()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->post('/mypage/profile', [
            'name' => '変更後の名前',
            'postal_code' => '123-4567',
            'address' => '東京都港区',
        ])->assertStatus(302);

        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => '変更後の名前']);
    }
}
