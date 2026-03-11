<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('display_media', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['slide', 'video']);
            $table->string('original_name');    // original upload filename
            $table->string('stored_path');      // path inside the public disk
            $table->unsignedSmallInteger('sort_order')->default(0); // slide ordering
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('display_media');
    }
};
