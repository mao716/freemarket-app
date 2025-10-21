<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Item;

class Category extends Model
{
	use HasFactory;

	protected $fillable = ['name'];

	// ====== カテゴリID定数 ======
	public const FASHION       = 1;  // ファッション
	public const ELECTRONICS   = 2;  // 家電
	public const INTERIOR      = 3;  // インテリア
	public const LADIES        = 4;  // レディース
	public const MENS          = 5;  // メンズ
	public const COSMETICS     = 6;  // コスメ
	public const BOOKS         = 7;  // 本
	public const GAMES         = 8;  // ゲーム
	public const SPORTS        = 9;  // スポーツ
	public const KITCHEN       = 10; // キッチン
	public const HANDMADE      = 11; // ハンドメイド
	public const ACCESSORY     = 12; // アクセサリー
	public const TOYS          = 13; // おもちゃ
	public const BABY_KIDS     = 14; // ベビー・キッズ

	public function items()
	{
		return $this->belongsToMany(Item::class, 'item_category')
			->withTimestamps();
	}
}
