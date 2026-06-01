<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->boolean('is_super_admin')->default(false)->after('password')->index();
        });

        User::query()->update(['is_super_admin' => true]);

        Schema::table('jobs', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });

        DB::table('jobs')
            ->select('company_name')
            ->whereNotNull('company_name')
            ->distinct()
            ->orderBy('company_name')
            ->get()
            ->each(function (object $job): void {
                $companyId = DB::table('companies')->insertGetId([
                    'name' => $job->company_name,
                    'slug' => str($job->company_name)->slug()->toString(),
                    'job_field_settings' => json_encode(\App\Models\Company::DEFAULT_JOB_FIELD_SETTINGS),
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('jobs')
                    ->where('company_name', $job->company_name)
                    ->update(['company_id' => $companyId]);
            });
    }

    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('company_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('company_id');
            $table->dropColumn('is_super_admin');
        });
    }
};
