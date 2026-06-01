<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('jobs')
            ->whereNull('reference')
            ->orWhere('reference', '')
            ->orderBy('id')
            ->select(['id'])
            ->chunkById(200, function ($jobs): void {
                foreach ($jobs as $job) {
                    DB::table('jobs')
                        ->where('id', $job->id)
                        ->update(['reference' => 'JOB-' . $job->id]);
                }
            });

        DB::table('jobs')
            ->whereNull('postcode')
            ->orWhere('postcode', '')
            ->update(['postcode' => 'TBC']);

        Schema::table('jobs', function (Blueprint $table): void {
            $table->string('reference')->nullable(false)->change();
            $table->string('postcode', 16)->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table): void {
            $table->string('reference')->nullable()->change();
            $table->string('postcode', 16)->nullable()->change();
        });
    }
};
