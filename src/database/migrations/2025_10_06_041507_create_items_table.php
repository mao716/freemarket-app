<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up(): void
	{
		Schema::create('items', function (Blueprint $table) {
			$table->id();
			$table->foreignId('user_id')->constrained()->cascadeOnDelete(); // 出品者（外部キー）

			$table->string('name', 255);                 // 商品名（必須）
			$table->string('brand', 255)->nullable();    // ブランド（任意）
			$table->text('description');                 // 説明（必須）
			$table->integer('price');                    // 価格（整数・必須）
			$table->tinyInteger('condition');            // 状態（0〜3などの段階・必須）

			$table->string('image_path', 255)->nullable(); // 画像パス（任意）※必須にするなら nullable を外す

			$table->timestamps();
		});
	}

	public function down(): void
	{
		Schema::dropIfExists('items');
	}
};
