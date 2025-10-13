<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
		User::firstOrCreate(
			['email' => 'seller@example.com'],
			[
				'name' => 'デフォルト出品者',
				'password' => Hash::make('password'),
				'postal_code' => '123-4567',
				'address' => '福岡県福岡市XX区YY町1-1-1',
				'building' => 'テストビル101',
				'avatar_path' => null,
			]
		);
    }
}
