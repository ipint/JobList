<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('header_logo_url')->nullable();
            $table->string('footer_logo_url')->nullable();
            $table->string('favicon_url')->nullable();
            $table->string('copyright_text')->nullable();
            $table->timestamps();
        });

        DB::table('site_settings')->insert([
            'id' => 1,
            'copyright_text' => '(c) ' . now()->year . ' JobList. All rights reserved.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
