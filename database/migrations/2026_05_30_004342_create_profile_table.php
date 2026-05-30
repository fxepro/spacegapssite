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
        Schema::create('profile', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('');
            $table->string('tagline')->nullable();        // e.g. "Writer · Researcher · Builder"
            $table->text('intro')->nullable();            // 1-3 sentence lead shown prominently
            $table->longText('summary')->nullable();      // full professional summary (HTML)
            $table->string('photo_url')->nullable();
            $table->string('video_url')->nullable();      // YouTube / Vimeo embed URL
            $table->string('location')->nullable();
            $table->string('email')->nullable();          // public contact email
            $table->json('social_links')->nullable();     // [{platform, url}]
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile');
    }
};
