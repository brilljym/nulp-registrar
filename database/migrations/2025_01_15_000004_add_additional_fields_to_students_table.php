<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up()
	{
		Schema::table('students', function (Blueprint $table) {
			if (!Schema::hasColumn('students', 'middle_name')) {
				$table->string('middle_name')->nullable();
			}
			if (!Schema::hasColumn('students', 'phone')) {
				$table->string('phone')->nullable();
			}
			if (!Schema::hasColumn('students', 'address')) {
				$table->text('address')->nullable();
			}
		});
	}

	public function down()
	{
		Schema::table('students', function (Blueprint $table) {
			if (Schema::hasColumn('students', 'address')) {
				$table->dropColumn('address');
			}
			if (Schema::hasColumn('students', 'phone')) {
				$table->dropColumn('phone');
			}
			if (Schema::hasColumn('students', 'middle_name')) {
				$table->dropColumn('middle_name');
			}
		});
	}
};
