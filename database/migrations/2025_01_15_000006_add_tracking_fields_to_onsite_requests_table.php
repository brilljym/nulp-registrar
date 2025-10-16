<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	public function up()
	{
		Schema::table('onsite_requests', function (Blueprint $table) {
			if (!Schema::hasColumn('onsite_requests', 'created_by')) {
				$table->unsignedBigInteger('created_by')->nullable()->after('id');
			}
			if (!Schema::hasColumn('onsite_requests', 'updated_by')) {
				$table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
			}
		});
	}

	public function down()
	{
		Schema::table('onsite_requests', function (Blueprint $table) {
			if (Schema::hasColumn('onsite_requests', 'updated_by')) {
				$table->dropColumn('updated_by');
			}
			if (Schema::hasColumn('onsite_requests', 'created_by')) {
				$table->dropColumn('created_by');
			}
		});
	}
};
