<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('xml_feeds', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->json('departments')->nullable();
            $table->json('employment_types')->nullable();
            $table->json('work_modes')->nullable();
            $table->json('experience_levels')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('company_xml_feed', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('xml_feed_id')->constrained('xml_feeds')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['xml_feed_id', 'company_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_xml_feed');
        Schema::dropIfExists('xml_feeds');
    }
};
