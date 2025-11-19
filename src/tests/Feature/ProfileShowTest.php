<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProfileShowTest extends TestCase
{
    use RefreshDatabase;

	public function test_プロフィール画面で必要な情報が表示される()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->get('/mypage')
            ->assertStatus(200)
            ->assertSee($user->name);
    }
}
