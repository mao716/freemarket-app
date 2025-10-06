<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
	use HasFactory, Notifiable;

	public function profile()
	{  // 1対1（hasOne）
		return $this->hasOne(Profile::class);
	}

	public function items()
	{    // 1対多（hasMany）
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
	{   // 自分が購入した注文
		return $this->hasMany(Order::class);
	}
}
