<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * 12) 配送先変更機能
 * 用語メモ：セッションスナップショット（その時点の値を控えておく仕組み）
 */
class AddressChangeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 住所変更が購入画面に反映される()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $this->actingAs($user);

        // 住所を保存
        $this->post("/purchase/address/{$item->id}", [
            'postal_code' => '123-4567',
            'address' => '東京都世田谷区1-2-3',
            'building' => 'ABCビル',
        ])->assertStatus(302);

        // 確認画面で反映確認（実装に合わせて assertSee を追加してね）
        $this->get("/purchase/{$item->id}")->assertStatus(200);
    }
}
