<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Item;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
		$seller = User::where('email', 'seller@example.com')->firstOrFail();

		$items = [
			['name' => '腕時計', 'brand' => 'Rolax', 'description' => 'スタイリッシュなデザインのメンズ腕時計', 'price' => 15000, 'condition' => Item::COND_EXCELLENT, 'image_path' => 'armani-clock.jpg'],
			['name' => 'HDD', 'brand' => '西芝', 'description' => '高速で信頼性の高いハードディスク', 'price' => 5000, 'condition' => Item::COND_GOOD, 'image_path' => 'hdd.jpg'],
			['name' => '玉ねぎ3束', 'brand' => null, 'description' => '新鮮な玉ねぎ3束のセット', 'price' => 300, 'condition' => Item::COND_SCRATCH, 'image_path' => 'onion.jpg'],
			['name' => '革靴', 'brand' => null, 'description' => 'クラシックなデザインの革靴', 'price' => 4000, 'condition' => Item::COND_BAD, 'image_path' => 'leather-shoes.jpg'],
			['name' => 'ノートPC', 'brand' => null, 'description' => '高性能なノートパソコン', 'price' => 45000, 'condition' => Item::COND_EXCELLENT, 'image_path' => 'laptop.jpg'],
			['name' => 'マイク', 'brand' => null, 'description' => '高音質のレコーディング用マイク', 'price' => 8000, 'condition' => Item::COND_GOOD, 'image_path' => 'mic.jpg'],
			['name' => 'ショルダーバッグ', 'brand' => null, 'description' => 'おしゃれなショルダーバッグ', 'price' => 3500, 'condition' => Item::COND_SCRATCH, 'image_path' => 'shoulder-bag.jpg'],
			['name' => 'タンブラー', 'brand' => null, 'description' => '使いやすいタンブラー', 'price' => 500, 'condition' => Item::COND_BAD, 'image_path' => 'tumbler.jpg'],
			['name' => 'コーヒーミル', 'brand' => 'Starbacks', 'description' => '手動のコーヒーミル', 'price' => 4000, 'condition' => Item::COND_EXCELLENT, 'image_path' => 'coffee-grinder.jpg'],
			['name' => 'メイクセット', 'brand' => null, 'description' => '便利なメイクアップセット', 'price' => 2500, 'condition' => Item::COND_GOOD, 'image_path' => 'makeup-set.jpg'],
		];

		foreach ($items as $data) {
			Item::create($data + ['user_id' => $seller->id]);
		}
    }
}
