<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_商品名で部分一致検索できる()
    {
        Item::factory()->create(['name' => '赤い靴']);
        Item::factory()->create(['name' => '青い靴']);

        $response = $this->get('/?keyword=赤');
        $response->assertStatus(200);
        $response->assertSee('赤い靴');
        $response->assertDontSee('青い靴');
    }
}
