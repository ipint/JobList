<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('reference')->nullable()->unique();
            $table->string('company_name');
            $table->longText('description');
            $table->longText('requirements')->nullable();
            $table->longText('benefits')->nullable();
            $table->string('employment_type')->index();
            $table->string('work_mode')->index();
            $table->string('experience_level')->nullable()->index();
            $table->string('status')->default('draft')->index();
            $table->foreignId('county_id')->constrained('uk_counties');
            $table->string('city');
            $table->string('postcode', 16)->nullable();
            $table->string('location_name')->nullable();
            $table->decimal('salary_min', 10, 2)->nullable();
            $table->decimal('salary_max', 10, 2)->nullable();
            $table->string('salary_currency', 3)->default('GBP');
            $table->string('salary_period')->nullable();
            $table->string('salary_text')->nullable();
            $table->boolean('is_salary_visible')->default(true);
            $table->string('application_url')->nullable();
            $table->string('application_email')->nullable();
            $table->boolean('visa_sponsorship_available')->default(false);
            $table->boolean('right_to_work_required')->default(true);
            $table->date('closing_date')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_featured')->default(false)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
