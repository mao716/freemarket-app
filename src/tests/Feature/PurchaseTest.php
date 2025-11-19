<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_購入画面が表示され購入確定できる()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $this->actingAs($user);

        $this->get("/purchase/{$item->id}")->assertStatus(200);

		$response = $this->post("/purchase/{$item->id}", [
			'payment' => \App\Models\Order::PAYMENT_KONBINI,
			'address' => '福岡県福岡市1-1-1',

		]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('orders', ['user_id' => $user->id, 'item_id' => $item->id]);
    }
}
