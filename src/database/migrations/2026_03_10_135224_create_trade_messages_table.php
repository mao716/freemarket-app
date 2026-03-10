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
		Schema::create('trade_messages', function (Blueprint $table) {
			$table->id();
			$table->foreignId('trade_id')->constrained()->onDelete('cascade');
			$table->foreignId('user_id')->constrained()->onDelete('cascade');
			$table->string('body', 400);
			$table->string('image_path', 255)->nullable();
			$table->timestamps();
			$table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('trade_messages');
	}
};
