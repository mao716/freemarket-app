<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	// users（認証用＋プロフィール統合）
	public function up(): void
	{
		Schema::create('users', function (Blueprint $table) {
			$table->id(); // 主キー（primary key）
			$table->string('name'); // 表示名
			$table->string('email')->unique(); // メール（重複不可）
			$table->timestamp('email_verified_at')->nullable(); // メール認証日時
			$table->string('password'); // ハッシュ化パスワード

			// ↓ ここから profiles を統合（プロフィール情報）
			$table->string('postal_code', 8)->nullable();  // 郵便番号（例: 123-4567）
			$table->string('address', 255)->nullable(); // 住所
			$table->string('building', 255)->nullable(); // 建物名
			$table->string('avatar_path', 255)->nullable(); // プロフ画像パス（storageやURL）

			$table->timestamps();
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('users');
	}
};
