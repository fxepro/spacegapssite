<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('papers', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content')->nullable();
            $table->longText('abstract')->nullable();
            $table->longText('references')->nullable();
            $table->string('featured_image')->nullable();
            $table->string('pdf_url')->nullable();
            $table->string('status')->default('draft');
            $table->string('author')->default('Admin');
            $table->integer('reading_time')->nullable();
            $table->boolean('featured')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('category_paper', function (Blueprint $table) {
            $table->foreignId('paper_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->primary(['paper_id', 'category_id']);
        });

        Schema::create('paper_tag', function (Blueprint $table) {
            $table->foreignId('paper_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $table->primary(['paper_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paper_tag');
        Schema::dropIfExists('category_paper');
        Schema::dropIfExists('papers');
    }
};
