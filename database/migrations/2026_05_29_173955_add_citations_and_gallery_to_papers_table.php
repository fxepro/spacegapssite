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
        Schema::table('papers', function (Blueprint $table) {
            // Structured citations: [{text, url}]
            $table->jsonb('citations')->nullable()->after('references');
            // Image gallery: [url, url, ...]
            $table->jsonb('gallery')->nullable()->after('citations');
        });
    }

    public function down(): void
    {
        Schema::table('papers', function (Blueprint $table) {
            $table->dropColumn(['citations', 'gallery']);
        });
    }
};
