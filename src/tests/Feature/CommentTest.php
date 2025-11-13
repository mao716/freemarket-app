<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_ログイン済みはコメントできる_未ログインはできない()
    {
        $item = Item::factory()->create();
        $this->post("/item/{$item->id}/comments", ['body' => 'NG'])->assertStatus(302);

        $user = User::factory()->create();
        $this->actingAs($user);

        $this->post("/item/{$item->id}/comments", ['body' => '素敵!'])->assertStatus(302);
        $this->assertDatabaseHas('comments', ['body' => '素敵!']);
    }
}
