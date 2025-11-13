<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaymentMethodTest extends TestCase
{
    use RefreshDatabase;

    public function test_支払い方法の選択が小計画面に反映される()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $this->actingAs($user);

        $this->post("/purchase/{$item->id}", [
            'payment' => 'konbini',
            'address' => '福岡県福岡市1-2-3',
        ])->assertStatus(302);
    }
}
