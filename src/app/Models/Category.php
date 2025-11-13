<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Item;

class Category extends Model
{
	use HasFactory;

	protected $fillable = ['name'];

	public const FASHION       = 1;
	public const ELECTRONICS   = 2;
	public const INTERIOR      = 3;
	public const LADIES        = 4;
	public const MENS          = 5;
	public const COSMETICS     = 6;
	public const BOOKS         = 7;
	public const GAMES         = 8;
	public const SPORTS        = 9;
	public const KITCHEN       = 10;
	public const HANDMADE      = 11;
	public const ACCESSORY     = 12;
	public const TOYS          = 13;
	public const BABY_KIDS     = 14;

	public function items()
	{
		return $this->belongsToMany(Item::class, 'item_category')
			->withTimestamps();
	}
}
