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
		Schema::create('items', function (Blueprint $table) {
			$table->id();
			$table->foreignId('user_id')->constrained()->cascadeOnDelete(); // 出品者
			$table->string('name', 255);
			$table->string('brand', 255)->nullable(); // ブランド名は任意
			$table->text('description'); // 説明
			$table->integer('price');    // 価格（整数）
			$table->tinyInteger('condition'); // コンディション
			$table->timestamps();
		});
	}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
