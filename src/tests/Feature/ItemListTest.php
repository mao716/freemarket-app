<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ItemListTest extends TestCase
{
    use RefreshDatabase;

    public function test_全商品が表示される()
    {
        $items = Item::factory()->count(3)->create();

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee($items->first()->name);
    }
}
