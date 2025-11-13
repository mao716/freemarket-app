<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * 4) 商品一覧取得
 * 用語メモ：ファクトリ（ダミー生成器）、アサーション（合否の判定）
 */
class ItemListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 全商品が表示される()
    {
        $items = Item::factory()->count(3)->create();

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee($items->first()->name);
    }
}
