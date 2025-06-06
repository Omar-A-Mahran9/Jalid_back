<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sliders', function (Blueprint $table) {
            $table->id();
            $table->string('title_en');
            $table->string('title_ar');
            $table->string('btn_title_en');
            $table->string('btn_title_ar');
            $table->string('btn_link');
            $table->longText('description_en');
            $table->longText('description_ar');
            $table->string('background')->nullable();
            $table->boolean('status')->default(TRUE);
            $table->boolean('is_video')->default(false); // New field
            $table->string('video_url')->nullable();     // New field
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sliders');
    }
};
