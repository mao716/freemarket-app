<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TradeMessage extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'trade_id',
		'user_id',
		'body',
		'image_path',
	];

	public function trade(): BelongsTo
	{
		return $this->belongsTo(Trade::class);
	}

	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}
}
