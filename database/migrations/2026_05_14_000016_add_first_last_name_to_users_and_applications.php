<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('first_name')->nullable()->after('id');
            $table->string('last_name')->nullable()->after('first_name');
        });

        Schema::table('applications', function (Blueprint $table): void {
            $table->string('candidate_first_name')->nullable()->after('job_id');
            $table->string('candidate_last_name')->nullable()->after('candidate_first_name');
        });

        DB::table('users')->orderBy('id')->chunkById(200, function ($users): void {
            foreach ($users as $user) {
                $fullName = trim((string) ($user->name ?? ''));
                if ($fullName === '') {
                    continue;
                }

                $parts = preg_split('/\s+/', $fullName) ?: [];
                $firstName = array_shift($parts) ?: $fullName;
                $lastName = trim(implode(' ', $parts));

                DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'first_name' => $firstName,
                        'last_name' => $lastName !== '' ? $lastName : null,
                    ]);
            }
        });

        DB::table('applications')->orderBy('id')->chunkById(200, function ($applications): void {
            foreach ($applications as $application) {
                $fullName = trim((string) ($application->candidate_name ?? ''));
                if ($fullName === '') {
                    continue;
                }

                $parts = preg_split('/\s+/', $fullName) ?: [];
                $firstName = array_shift($parts) ?: $fullName;
                $lastName = trim(implode(' ', $parts));

                DB::table('applications')
                    ->where('id', $application->id)
                    ->update([
                        'candidate_first_name' => $firstName,
                        'candidate_last_name' => $lastName !== '' ? $lastName : null,
                    ]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table): void {
            $table->dropColumn(['candidate_first_name', 'candidate_last_name']);
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['first_name', 'last_name']);
        });
    }
};
