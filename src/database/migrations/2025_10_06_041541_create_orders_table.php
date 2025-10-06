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
		Schema::create('orders', function (Blueprint $table) {
			$table->id();
			$table->foreignId('user_id')->constrained()->cascadeOnDelete(); // 購入者
			$table->foreignId('item_id')->constrained()->cascadeOnDelete(); // 対象商品
			$table->text('address_snapshot'); // 購入後にプロフィールを変更しても、当時の配送先は記録として残す
			$table->enum('payment_method', ['card', 'konbini']); // 支払い方法
			$table->enum('status', ['pending', 'paid', 'canceled'])->default('pending'); // 状態
			$table->timestamps();

			$table->unique('item_id'); // 1商品は1回しか売れない（再購入不可）
		});
	}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
