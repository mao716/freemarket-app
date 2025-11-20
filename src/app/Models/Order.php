<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Item;

class Order extends Model
{
	use HasFactory;

	public const PAYMENT_CARD    = 'card';
	public const PAYMENT_KONBINI = 'konbini';

	public const STATUS_PENDING  = 'pending';
	public const STATUS_PAID     = 'paid';
	public const STATUS_CANCELED = 'canceled';

	protected $fillable = [
		'user_id',
		'item_id',
		'address',
		'payment',
		'status',
	];

	protected $attributes = [
		'status' => self::STATUS_PENDING,
	];

	public function user()
	{
		return $this->belongsTo(User::class);
	}
	public function item()
	{
		return $this->belongsTo(Item::class);
	}
}
