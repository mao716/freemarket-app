<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * 5) マイリスト一覧取得
 * 用語メモ：クエリパラメータ（URLの?以降の追加情報）
 */
class MylistTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ログインユーザーのマイリストが表示される()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/?tab=mylist');
        $response->assertStatus(200);
    }
}
