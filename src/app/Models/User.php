<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable implements MustVerifyEmail
{
	use HasFactory, Notifiable;

	protected $fillable = [
		'name',
		'email',
		'password',
		'postal_code',
		'address',
		'building',
		'avatar_path',
	];

	protected $appends = ['avatar_url'];

	public function getAvatarUrlAttribute(): string
	{
		$path = $this->avatar_path;

		if (empty($path)) {
			return asset('images/image-placeholder.png');
		}

		return Storage::url($path);
	}

	public function items()
	{
		return $this->hasMany(Item::class);
	}

	public function likes()
	{
		return $this->hasMany(Like::class);
	}

	public function comments()
	{
		return $this->hasMany(Comment::class);
	}

	public function orders()
	{
		return $this->hasMany(Order::class);
	}
}
