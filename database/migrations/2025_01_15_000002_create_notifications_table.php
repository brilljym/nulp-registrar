<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up()
	{
		Schema::create('notifications', function (Blueprint $table) {
			$table->id();
			$table->string('type')->nullable();
			$table->morphs('notifiable');
			$table->text('data')->nullable();
			$table->timestamp('read_at')->nullable();
			$table->timestamps();
		});
	}

	public function down()
	{
		Schema::dropIfExists('notifications');
	}
};
