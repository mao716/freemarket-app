<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
		Schema::create('profiles', function (Blueprint $table) {
			$table->id(); // 主キー（primary key）
			$table->foreignId('user_id')->constrained()->cascadeOnDelete(); // 外部キー（他テーブルと紐付け）
			$table->string('postal_code', 8);   // 郵便番号（ハイフン込み8文字）
			$table->string('address', 255);     // 住所
			$table->string('building', 255)->nullable();    // 建物名（任意）
			$table->string('avatar_path', 255)->nullable(); // プロフ画像パス（任意）
			$table->timestamps(); // created_at / updated_at
		});
	}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
