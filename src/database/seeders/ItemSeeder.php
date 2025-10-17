<?php

namespace Database\Seeders;

use Illuminate\Support\Arr;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Item;
use App\Models\Category;

class ItemSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		// UserSeederで作ったユーザーを全部取得
		$users = User::get();

		$items = [
			[
				'name' => '腕時計',
				'brand' => 'Rolax',
				'description' =>
				'スタイリッシュなデザインのメンズ腕時計',
				'price' => 15000,
				'condition' => Item::COND_EXCELLENT,
				'image_path' => 'armani-clock.jpg',
				'categories' => [Category::FASHION, Category::MENS]
			],

			[
				'name' => 'HDD',
				'brand' => '西芝',
				'description' => '高速で信頼性の高いハードディスク',
				'price' => 5000,
				'condition' => Item::COND_GOOD,
				'image_path' => 'hdd.jpg',
				'categories' => [Category::ELECTRONICS]
			],

			[
				'name' => '玉ねぎ3束',
				'brand' => null,
				'description' => '新鮮な玉ねぎ3束のセット',
				'price' => 300,
				'condition' => Item::COND_SCRATCH,
				'image_path' => 'onion.jpg',
				'categories' => [Category::KITCHEN]
			],

			[
				'name' => '革靴',
				'brand' => null,
				'description' => 'クラシックなデザインの革靴',
				'price' => 4000,
				'condition' => Item::COND_BAD,
				'image_path' => 'leather-shoes.jpg',
				'categories' => [Category::FASHION, Category::MENS]
			],

			[
				'name' => 'ノートPC',
				'brand' => null,
				'description' => '高性能なノートパソコン',
				'price' => 45000,
				'condition' => Item::COND_EXCELLENT,
				'image_path' => 'laptop.jpg',
				'categories' => [Category::ELECTRONICS]
			],

			[
				'name' => 'マイク',
				'brand' => null,
				'description' => '高音質のレコーディング用マイク',
				'price' => 8000,
				'condition' => Item::COND_GOOD,
				'image_path' => 'mic.jpg',
				'categories' => [Category::ELECTRONICS]
			],

			[
				'name' => 'ショルダーバッグ',
				'brand' => null,
				'description' => 'おしゃれなショルダーバッグ',
				'price' => 3500,
				'condition' => Item::COND_SCRATCH,
				'image_path' => 'shoulder-bag.jpg',
				'categories' => [Category::FASHION, Category::LADIES]
			],

			[
				'name' => 'タンブラー',
				'brand' => null,
				'description' => '使いやすいタンブラー',
				'price' => 500,
				'condition' => Item::COND_BAD,
				'image_path' => 'tumbler.jpg',
				'categories' => [Category::KITCHEN]
			],

			[
				'name' => 'コーヒーミル',
				'brand' => 'Starbacks',
				'description' => '手動のコーヒーミル',
				'price' => 4000,
				'condition' => Item::COND_EXCELLENT,
				'image_path' => 'coffee-grinder.jpg',
				'categories' => [Category::KITCHEN]
			],

			[
				'name' => 'メイクセット',
				'brand' => null,
				'description' => '便利なメイクアップセット',
				'price' => 2500,
				'condition' => Item::COND_GOOD,
				'image_path' => 'makeup-set.jpg',
				'categories' => [Category::COSMETICS]
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
