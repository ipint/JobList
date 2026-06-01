<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('application_flags')
            ->where('value', 'progress')
            ->update([
                'label' => 'Previously Applied',
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        DB::table('application_flags')
            ->where('value', 'progress')
            ->update([
                'label' => 'Progress',
                'updated_at' => now(),
            ]);
    }
};

