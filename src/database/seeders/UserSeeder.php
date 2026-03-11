<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
	public function run(): void
	{
		$plainPassword = 'password';

		User::factory()->create([
			'name' => '出品者A',
			'email' => 'seller@example.com',
			'password' => Hash::make($plainPassword),
			'postal_code' => '123-4567',
			'address' => '福岡県福岡市XX区YY町1-1-1',
			'building' => 'テストビル101',
			'avatar_path' => null,
		]);

		User::factory()->create([
			'name' => '出品者B',
			'email' => 'seller2@example.com',
			'password' => Hash::make($plainPassword),
			'postal_code' => '123-4567',
			'address' => '福岡県福岡市XX区YY町1-1-1',
			'building' => 'テストビル102',
			'avatar_path' => null,
		]);

		User::factory()->create([
			'name' => '出品者C',
			'email' => 'seller3@example.com',
			'password' => Hash::make($plainPassword),
			'postal_code' => '123-4567',
			'address' => '福岡県福岡市XX区YY町1-1-1',
			'building' => 'テストビル103',
			'avatar_path' => null,
		]);
	}
}
