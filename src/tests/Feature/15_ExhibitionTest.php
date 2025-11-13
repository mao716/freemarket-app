<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * 15) 出品商品情報登録
 * 用語メモ：バリデーション（入力チェック）、ストレージ（ファイル保存領域）
 */
class ExhibitionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 出品画面が表示され商品を登録できる()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->get('/sell')->assertStatus(200);

        $this->post('/sell', [
            'name' => 'テスト商品',
            'brand' => 'BrandX',
            'description' => '説明文です',
            'price' => 5000,
            'condition' => 1,
            // 'categories' => [1, 2], // カテゴリ連携は実装に合わせて調整
        ])->assertStatus(302);
    }
}
