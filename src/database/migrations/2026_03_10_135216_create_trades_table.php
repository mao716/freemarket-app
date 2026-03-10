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
		Schema::create('trades', function (Blueprint $table) {
			$table->id();
			$table->foreignId('order_id')->unique()->constrained()->onDelete('cascade');
			$table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
			$table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
			$table->tinyInteger('status');
			$table->timestamp('last_message_at')->nullable();
			$table->integer('buyer_unread_count')->default(0);
			$table->integer('seller_unread_count')->default(0);
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('trades');
	}
};
