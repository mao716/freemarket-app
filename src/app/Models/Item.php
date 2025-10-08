<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Category;
use App\Models\Like;
use App\Models\Comment;
use App\Models\Order;

class Item extends Model
{
	use HasFactory;

	protected $fillable = [
		'user_id',
		'name',
		'brand',
		'description',
		'price',
		'condition',
		'image_path',
	];

	public const CONDITION = [
		1 => '良好',
		2 => '目立った傷や汚れなし',
		3 => 'やや傷や汚れあり',
		4 => '状態が悪い',
	];

	// 出品者
	public function user()
	{
		return $this->belongsTo(User::class);
	}

	// カテゴリ（多対多）
	public function categories()
	{
		return $this->belongsToMany(Category::class, 'item_category')
			->withTimestamps();
	}

	// いいね（1対多）
	public function likes()
	{
		return $this->hasMany(Like::class);
	}

	// コメント（1対多）
	public function comments()
	{
		return $this->hasMany(Comment::class);
	}

	// 売買（1対1：売れたら注文が一つ付く想定）
	public function order()
	{
		return $this->hasOne(Order::class);
	}

	// 売却済かどうか
	protected $appends = ['is_sold'];
	public function getIsSoldAttribute()
	{
		return $this->order()->exists();
	}

	// スコープ（クエリの掃き出し便利関数）
	public function scopeAvailable($q)
	{
		return $q->doesntHave('order');
	}
	public function scopeSold($q)
	{
		return $q->has('order');
	}
}
