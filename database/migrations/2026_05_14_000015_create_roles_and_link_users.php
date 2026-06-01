<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->json('permissions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->foreignId('role_id')->nullable()->after('is_super_admin')->constrained('roles')->nullOnDelete();
        });

        $timestamp = now();
        $fullPermissions = collect([
            'jobs',
            'companies',
            'job_fields',
            'applications',
            'job_attributes',
            'media',
            'site_settings',
            'users',
            'roles',
        ])
            ->flatMap(fn (string $section): array => collect(['view', 'create', 'edit', 'delete'])
                ->map(fn (string $action): string => "{$section}.{$action}")
                ->all())
            ->values()
            ->all();

        DB::table('roles')->insert([
            'name' => 'Administrator',
            'slug' => 'administrator',
            'permissions' => json_encode($fullPermissions),
            'is_active' => true,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('role_id');
        });

        Schema::dropIfExists('roles');
    }
};
