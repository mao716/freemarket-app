<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * 9) コメント送信機能
 * 用語メモ：FormRequest（入力チェック用クラス）、バリデーションメッセージ（エラー文言）
 */
class CommentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ログイン済みはコメントできる_未ログインはできない()
    {
        $item = Item::factory()->create();

        // 未ログインはNG（ログイン画面へリダイレクト想定）
        $this->post("/item/{$item->id}/comments", ['body' => 'NG'])->assertStatus(302);

        // ログイン済みはOK
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->post("/item/{$item->id}/comments", ['body' => '素敵!'])->assertStatus(302);
        $this->assertDatabaseHas('comments', ['body' => '素敵!']);
    }
}
