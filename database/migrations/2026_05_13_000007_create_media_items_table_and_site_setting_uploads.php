<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_items', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('alt_text')->nullable();
            $table->string('disk')->default('public');
            $table->string('path');
            $table->string('original_name')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->timestamps();

            $table->index('mime_type');
        });

        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('header_logo_path')->nullable()->after('header_logo_url');
            $table->string('footer_logo_path')->nullable()->after('footer_logo_url');
            $table->string('favicon_path')->nullable()->after('favicon_url');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'header_logo_path',
                'footer_logo_path',
                'favicon_path',
            ]);
        });

        Schema::dropIfExists('media_items');
    }
};
