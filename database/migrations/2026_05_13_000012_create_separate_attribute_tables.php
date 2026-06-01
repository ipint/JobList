<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->createOptionTable('departments');
        $this->createOptionTable('employment_types');
        $this->createOptionTable('work_modes');
        $this->createOptionTable('experience_levels');
        $this->createOptionTable('application_statuses');
        $this->createOptionTable('application_flags');

        $this->copyFromJobAttributes('department', 'departments');
        $this->copyFromJobAttributes('employment_type', 'employment_types');
        $this->copyFromJobAttributes('work_mode', 'work_modes');
        $this->copyFromJobAttributes('experience_level', 'experience_levels');
        $this->copyFromJobAttributes('application_status', 'application_statuses');
        $this->copyFromJobAttributes('application_flag', 'application_flags');
    }

    public function down(): void
    {
        Schema::dropIfExists('application_flags');
        Schema::dropIfExists('application_statuses');
        Schema::dropIfExists('experience_levels');
        Schema::dropIfExists('work_modes');
        Schema::dropIfExists('employment_types');
        Schema::dropIfExists('departments');
    }

    protected function createOptionTable(string $table): void
    {
        Schema::create($table, function (Blueprint $table): void {
            $table->id();
            $table->string('label');
            $table->string('value')->unique();
            $table->string('color')->nullable();
            $table->unsignedInteger('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    protected function copyFromJobAttributes(string $type, string $table): void
    {
        DB::table('job_attributes')
            ->where('type', $type)
            ->orderBy('display_order')
            ->orderBy('id')
            ->get()
            ->each(function ($row) use ($table): void {
                DB::table($table)->updateOrInsert(
                    ['value' => $row->value],
                    [
                        'label' => $row->label,
                        'color' => $row->color,
                        'display_order' => $row->display_order,
                        'is_active' => $row->is_active,
                        'created_at' => $row->created_at ?? now(),
                        'updated_at' => $row->updated_at ?? now(),
                    ],
                );
            });
    }
};

