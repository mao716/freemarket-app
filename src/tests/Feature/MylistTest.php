<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MylistTest extends TestCase
{
    use RefreshDatabase;

    public function test_ログインユーザーのマイリストが表示される()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/?tab=mylist');
        $response->assertStatus(200);
    }
}
