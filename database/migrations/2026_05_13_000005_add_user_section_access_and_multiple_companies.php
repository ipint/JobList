<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['company_id', 'user_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->json('accessible_sections')->nullable()->after('is_super_admin');
        });

        DB::table('users')
            ->whereNotNull('company_id')
            ->orderBy('id')
            ->get(['id', 'company_id'])
            ->each(function (object $user): void {
                DB::table('company_user')->updateOrInsert(
                    [
                        'company_id' => $user->company_id,
                        'user_id' => $user->id,
                    ],
                    [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                );
            });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('accessible_sections');
        });

        Schema::dropIfExists('company_user');
    }
};
