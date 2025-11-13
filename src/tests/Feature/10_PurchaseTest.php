<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * 10) 商品購入機能
 * 用語メモ：トランザクション（まとめて処理し失敗時に巻き戻す仕組み）
 */
class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 購入画面が表示され購入確定できる()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $this->actingAs($user);

        $this->get("/purchase/{$item->id}")->assertStatus(200);

        $response = $this->post("/purchase/{$item->id}", [
            'payment' => 'card',
            'address' => '東京都渋谷区1-1-1',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('orders', ['user_id' => $user->id, 'item_id' => $item->id]);
    }
}
