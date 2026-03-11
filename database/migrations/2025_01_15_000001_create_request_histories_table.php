<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up()
	{
		Schema::create('request_histories', function (Blueprint $table) {
			$table->id();
			$table->unsignedBigInteger('requestable_id')->nullable();
			$table->string('requestable_type')->nullable();
			$table->unsignedBigInteger('user_id')->nullable();
			$table->string('action')->nullable();
			$table->text('notes')->nullable();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::dropIfExists('request_histories');
	}
};
