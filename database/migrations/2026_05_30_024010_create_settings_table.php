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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            // Nav visibility — each controls whether the link shows in the public top nav
            $table->boolean('nav_home')->default(true);
            $table->boolean('nav_blog')->default(true);
            $table->boolean('nav_portfolio')->default(true);
            $table->boolean('nav_papers')->default(true);
            $table->boolean('nav_gallery')->default(true);
            $table->boolean('nav_books')->default(true);
            $table->boolean('nav_videos')->default(true);
            $table->boolean('nav_about')->default(true);
            $table->boolean('nav_contact')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
