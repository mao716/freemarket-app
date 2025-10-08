<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Item;

class Order extends Model
{
	use HasFactory;

	const PAYMENT_CARD = 'card';
	const PAYMENT_KONBINI = 'konbini';

	const STATUS_PENDING  = 'pending';
	const STATUS_PAID     = 'paid';
	const STATUS_CANCELED = 'canceled';

	protected $fillable = [
		'user_id',
		'item_id',
		'address',
		'payment',
		'status',
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
