<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * 8) いいね機能
 * 用語メモ：トグル（ON/OFF切替）、ユニーク制約（重複禁止ルール）
 */
class LikeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function いいねできて取り消しもできる()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $this->actingAs($user);

        // いいね
        $this->post("/item/{$item->id}/like")->assertStatus(302);
        $this->assertDatabaseHas('likes', ['user_id' => $user->id, 'item_id' => $item->id]);

        // いいね解除
        $this->delete("/item/{$item->id}/like")->assertStatus(302);
        $this->assertDatabaseMissing('likes', ['user_id' => $user->id, 'item_id' => $item->id]);
    }
}
