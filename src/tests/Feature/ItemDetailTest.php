<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ItemDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_商品詳細ページに必要情報が表示される()
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
		$response->assertSee('5,000');
        $response->assertSee('説明文');
    }
}
