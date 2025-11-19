<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AddressChangeTest extends TestCase
{
    use RefreshDatabase;

    public function test_住所変更が購入画面に反映される()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $this->actingAs($user);

        $this->post("/purchase/address/{$item->id}", [
            'postal_code' => '123-4567',
            'address' => '福岡県福岡市1-2-3',
            'building' => 'ABCビル',
        ])->assertStatus(302);

        $this->get("/purchase/{$item->id}")->assertStatus(200);
    }
}
