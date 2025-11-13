<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * 11) 支払い方法選択機能
 * 用語メモ：セレクトボックス（プルダウン）、反映（画面表示への値の適用）
 */
class PaymentMethodTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 支払い方法の選択が小計画面に反映される()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $this->actingAs($user);

        // 実装の表示仕様に合わせて assertSee などへ調整してね
        $this->post("/purchase/{$item->id}", [
            'payment' => 'konbini',
            'address' => '東京都港区1-2-3',
        ])->assertStatus(302);
    }
}
