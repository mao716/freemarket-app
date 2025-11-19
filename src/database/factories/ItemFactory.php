<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
	protected $model = Item::class;

	public function definition()
	{
		return [
			'user_id' => User::factory(),
			'name' => $this->faker->words(2, true),
			'brand' => $this->faker->company(),
			'description' => $this->faker->sentence(),
			'price' => 1000,
			'condition' => array_rand(Item::CONDITION),
			'image_path' => 'test.jpg',
		];
	}
}
