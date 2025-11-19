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

	public const COND_EXCELLENT  = 1;
	public const COND_GOOD       = 2;
	public const COND_SCRATCH    = 3;
	public const COND_BAD        = 4;

	public const CONDITION = [
		self::COND_EXCELLENT  => '良好',
		self::COND_GOOD       => '目立った傷や汚れなし',
		self::COND_SCRATCH    => 'やや傷や汚れあり',
		self::COND_BAD        => '状態が悪い',
	];

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function categories()
	{
		return $this->belongsToMany(Category::class, 'item_category')
			->withTimestamps();
	}

	public function likes()
	{
		return $this->hasMany(Like::class);
	}

	public function comments()
	{
		return $this->hasMany(Comment::class)->latest();
	}

	public function order()
	{
		return $this->hasOne(Order::class);
	}

	protected $appends = ['is_sold'];
	public function getIsSoldAttribute()
	{
		return $this->order()->exists();
	}

	public function scopeAvailable($query)
	{
		return $query->doesntHave('order');
	}
	public function scopeSold($query)
	{
		return $query->has('order');
	}

	public function getImageUrlAttribute(): string
	{
		$imagePath = $this->image_path;

		if (preg_match('#^https?://#', $imagePath)) {
			return $imagePath;
		}

		return asset('storage/' . $imagePath);
	}
}
