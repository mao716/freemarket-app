<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProfileEditTest extends TestCase
{
    use RefreshDatabase;

    public function test_プロフィールを更新できる()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->post('/mypage/profile', [
            'name' => '変更後の名前',
            'postal_code' => '123-4567',
            'address' => '福岡県福岡市',
        ])->assertStatus(302);

        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => '変更後の名前']);
    }
}
