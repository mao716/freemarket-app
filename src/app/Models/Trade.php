<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Trade extends Model
{
	protected $fillable = [
		'order_id',
		'buyer_id',
		'seller_id',
		'status',
		'last_message_at',
		'buyer_unread_count',
		'seller_unread_count',
	];

	public function order(): BelongsTo
	{
		return $this->belongsTo(Order::class);
	}

	public function buyer(): BelongsTo
	{
		return $this->belongsTo(User::class, 'buyer_id');
	}

	public function seller(): BelongsTo
	{
		return $this->belongsTo(User::class, 'seller_id');
	}

	public function messages(): HasMany
	{
		return $this->hasMany(TradeMessage::class);
	}

	public function reviews(): HasMany
	{
		return $this->hasMany(TradeReview::class);
	}
}
