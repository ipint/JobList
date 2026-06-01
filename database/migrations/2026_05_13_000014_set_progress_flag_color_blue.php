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
                'color' => 'primary',
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        DB::table('application_flags')
            ->where('value', 'progress')
            ->update([
                'color' => 'warning',
                'updated_at' => now(),
            ]);
    }
};

