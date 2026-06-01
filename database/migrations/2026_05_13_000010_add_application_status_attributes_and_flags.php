<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_attributes', function (Blueprint $table) {
            $table->string('color')->nullable()->after('value');
        });

        $statuses = [
            ['value' => 'new', 'label' => 'New', 'color' => 'gray'],
            ['value' => 'reviewing', 'label' => 'Reviewing', 'color' => 'info'],
            ['value' => 'shortlisted', 'label' => 'Shortlisted', 'color' => 'success'],
            ['value' => 'interviewing', 'label' => 'Interviewing', 'color' => 'primary'],
            ['value' => 'offered', 'label' => 'Offered', 'color' => 'warning'],
            ['value' => 'hired', 'label' => 'Hired', 'color' => 'success'],
            ['value' => 'rejected', 'label' => 'Rejected', 'color' => 'danger'],
            ['value' => 'withdrawn', 'label' => 'Withdrawn', 'color' => 'gray'],
        ];

        foreach ($statuses as $index => $status) {
            DB::table('job_attributes')->updateOrInsert(
                [
                    'type' => 'application_status',
                    'value' => $status['value'],
                ],
                [
                    'label' => $status['label'],
                    'color' => $status['color'],
                    'display_order' => $index + 1,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            );
        }

        Schema::table('applications', function (Blueprint $table) {
            $table->string('flag')->nullable()->after('status')->index();
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn('flag');
        });

        DB::table('job_attributes')
            ->where('type', 'application_status')
            ->delete();

        Schema::table('job_attributes', function (Blueprint $table) {
            $table->dropColumn('color');
        });
    }
};
