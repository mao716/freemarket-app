<?php

namespace Database\Seeders;

use Illuminate\Support\Arr;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Item;
use App\Models\Category;

class ItemSeeder extends Seeder
{
	public function run(): void
	{
		$users = User::get();

		$items = [
			[
				'name'        => '腕時計',
				'brand'       => 'Rolax',
				'description' => 'スタイリッシュなデザインのメンズ腕時計',
				'price'       => 15000,
				'condition'   => Item::COND_EXCELLENT,
				'image_path'  => 'items/armani-clock.jpg',
				'categories'  => [Category::FASHION, Category::MENS],
			],
			[
				'name'        => 'HDD',
				'brand'       => '西芝',
				'description' => '高速で信頼性の高いハードディスク',
				'price'       => 5000,
				'condition'   => Item::COND_GOOD,
				'image_path'  => 'items/hdd.jpg',
				'categories'  => [Category::ELECTRONICS],
			],
			[
				'name'        => '玉ねぎ3束',
				'brand'       => null,
				'description' => '新鮮な玉ねぎ3束のセット',
				'price'       => 300,
				'condition'   => Item::COND_SCRATCH,
				'image_path'  => 'items/onion.jpg',
				'categories'  => [Category::KITCHEN],
			],
			[
				'name'        => '革靴',
				'brand'       => null,
				'description' => 'クラシックなデザインの革靴',
				'price'       => 4000,
				'condition'   => Item::COND_BAD,
				'image_path'  => 'items/leather-shoes.jpg',
				'categories'  => [Category::FASHION, Category::MENS],
			],
			[
				'name'        => 'ノートPC',
				'brand'       => null,
				'description' => '高性能なノートパソコン',
				'price'       => 45000,
				'condition'   => Item::COND_EXCELLENT,
				'image_path'  => 'items/laptop.jpg',
				'categories'  => [Category::ELECTRONICS],
			],
			[
				'name'        => 'マイク',
				'brand'       => null,
				'description' => '高音質のレコーディング用マイク',
				'price'       => 8000,
				'condition'   => Item::COND_GOOD,
				'image_path'  => 'items/mic.jpg',
				'categories'  => [Category::ELECTRONICS],
			],
			[
				'name'        => 'ショルダーバッグ',
				'brand'       => null,
				'description' => 'おしゃれなショルダーバッグ',
				'price'       => 3500,
				'condition'   => Item::COND_SCRATCH,
				'image_path'  => 'items/shoulder-bag.jpg',
				'categories'  => [Category::FASHION, Category::LADIES],
			],
			[
				'name'        => 'タンブラー',
				'brand'       => null,
				'description' => '使いやすいタンブラー',
				'price'       => 500,
				'condition'   => Item::COND_BAD,
				'image_path'  => 'items/tumbler.jpg',
				'categories'  => [Category::KITCHEN],
			],
			[
				'name'        => 'コーヒーミル',
				'brand'       => 'Starbacks',
				'description' => '手動のコーヒーミル',
				'price'       => 4000,
				'condition'   => Item::COND_EXCELLENT,
				'image_path'  => 'items/coffee-grinder.jpg',
				'categories'  => [Category::KITCHEN],
			],
			[
				'name'        => 'メイクセット',
				'brand'       => null,
				'description' => '便利なメイクアップセット',
				'price'       => 2500,
				'condition'   => Item::COND_GOOD,
				'image_path'  => 'items/makeup-set.jpg',
				'categories'  => [Category::COSMETICS],
			],
		];

		foreach ($items as $i => $data) {
			$categoryIdsForThis = Arr::pull($data, 'categories', []);

			$item = Item::create($data + [
				'user_id' => $users[$i % $users->count()]->id,
			]);

			if (!empty($categoryIdsForThis)) {
				$item->categories()->attach($categoryIdsForThis);
			}
		}
	}
}
