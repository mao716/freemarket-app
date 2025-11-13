<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * 6) 商品検索機能
 * 用語メモ：部分一致（LIKE検索：一部の文字が含まれていればヒット）
 */
class SearchTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 商品名で部分一致検索できる()
    {
        Item::factory()->create(['name' => '赤い靴']);
        Item::factory()->create(['name' => '青い靴']);

        $response = $this->get('/?keyword=赤');
        $response->assertStatus(200);
        $response->assertSee('赤い靴');
        $response->assertDontSee('青い靴');
    }
}
