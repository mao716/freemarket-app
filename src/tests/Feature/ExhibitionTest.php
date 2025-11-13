<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExhibitionTest extends TestCase
{
    use RefreshDatabase;

    public function test_出品画面が表示され商品を登録できる()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->get('/sell')->assertStatus(200);

        $this->post('/sell', [
            'name' => 'テスト商品',
            'brand' => 'BrandX',
            'description' => '説明文です',
            'price' => 5000,
            'condition' => 1,
        ])->assertStatus(302);
    }
}
