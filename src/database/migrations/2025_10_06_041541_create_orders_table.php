<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
		Schema::create('orders', function (Blueprint $table) {
			$table->id();
			$table->foreignId('user_id')->constrained()->cascadeOnDelete();
			$table->foreignId('item_id')->constrained()->cascadeOnDelete();

			$table->text('address');
			$table->enum('payment', ['card', 'konbini']);
			$table->enum('status', ['pending', 'paid', 'canceled'])->default('pending');
			$table->timestamps();

			$table->unique('item_id');
		});
	}

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
