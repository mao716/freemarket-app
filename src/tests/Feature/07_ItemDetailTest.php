<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * 7) 商品詳細情報取得
 * 用語メモ：ルーティング（URLと処理の対応づけ）、ビュー（画面テンプレート）
 */
class ItemDetailTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 商品詳細ページに必要情報が表示される()
    {
        $item = Item::factory()->create([
            'name' => 'テスト商品',
            'brand' => 'BRAND',
            'price' => 5000,
            'description' => '説明文',
            'condition' => 1,
        ]);

        $response = $this->get("/item/{$item->id}");
        $response->assertStatus(200);
        $response->assertSee('テスト商品');
        $response->assertSee('BRAND');
        $response->assertSee('5000');
        $response->assertSee('説明文');
    }
}
