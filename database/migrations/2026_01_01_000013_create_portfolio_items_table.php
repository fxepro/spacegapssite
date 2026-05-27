<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('portfolio_items', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content')->nullable();
            $table->string('featured_image')->nullable();
            $table->string('status')->default('draft');
            $table->string('author')->default('Admin');
            $table->date('project_date')->nullable();
            $table->string('client')->nullable();
            $table->string('role')->nullable();
            $table->string('external_url')->nullable();
            $table->json('gallery')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('featured')->default(false);
            $table->timestamps();
        });

        Schema::create('category_portfolio_item', function (Blueprint $table) {
            $table->foreignId('portfolio_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->primary(['portfolio_item_id', 'category_id']);
        });

        Schema::create('portfolio_item_tag', function (Blueprint $table) {
            $table->foreignId('portfolio_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $table->primary(['portfolio_item_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_item_tag');
        Schema::dropIfExists('category_portfolio_item');
        Schema::dropIfExists('portfolio_items');
    }
};
