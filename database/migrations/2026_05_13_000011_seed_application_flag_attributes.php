<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $flags = [
            ['value' => 'reject', 'label' => 'Reject', 'color' => 'danger'],
            ['value' => 'shortlist', 'label' => 'Shortlist', 'color' => 'success'],
            ['value' => 'progress', 'label' => 'Progress', 'color' => 'warning'],
        ];

        foreach ($flags as $index => $flag) {
            DB::table('job_attributes')->updateOrInsert(
                [
                    'type' => 'application_flag',
                    'value' => $flag['value'],
                ],
                [
                    'label' => $flag['label'],
                    'color' => $flag['color'],
                    'display_order' => $index + 1,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            );
        }
    }

    public function down(): void
    {
        DB::table('job_attributes')
            ->where('type', 'application_flag')
            ->whereIn('value', ['reject', 'shortlist', 'progress'])
            ->delete();
    }
};

