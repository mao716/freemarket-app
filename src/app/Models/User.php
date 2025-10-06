<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail; （メール認証を使う時に追加）
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// class User extends Authenticatable implements MustVerifyEmail （メール認証を使う時に書き換え）
class User extends Authenticatable
{
	use HasFactory, Notifiable;

	public function profile()
	{  // 1対1（hasOne：1ユーザーに1プロフィール）
		return $this->hasOne(Profile::class);
	}

	public function items()
	{  // 1対多（hasMany：1ユーザーが複数商品を出品）
		return $this->hasMany(Item::class);
	}

	public function likes()
	{  // 1対多（hasMany：1ユーザーが複数いいね）
		return $this->hasMany(Like::class);
	}

	public function comments()
	{  // 1対多（hasMany：1ユーザーが複数コメント）
		return $this->hasMany(Comment::class);
	}

	public function orders()
	{   // 1対多（hasMany：1ユーザーが複数注文を持つ）
		return $this->hasMany(Order::class);
	}
}
