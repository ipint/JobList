<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_attributes', function (Blueprint $table) {
            $table->id();
            $table->string('type')->index();
            $table->string('label');
            $table->string('value');
            $table->unsignedInteger('display_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            $table->unique(['type', 'value']);
        });

        $defaults = [
            'department' => [
                'Technology',
                'Sales',
                'Marketing',
                'Finance',
                'Human Resources',
                'Operations',
                'Customer Support',
                'Product',
                'Legal',
                'Procurement',
            ],
            'employment_type' => [
                'full_time' => 'Full-time',
                'part_time' => 'Part-time',
                'contract' => 'Contract',
                'temporary' => 'Temporary',
                'internship' => 'Internship',
            ],
            'work_mode' => [
                'on_site' => 'On-site',
                'hybrid' => 'Hybrid',
                'remote' => 'Remote',
            ],
            'experience_level' => [
                'entry' => 'Entry',
                'junior' => 'Junior',
                'mid' => 'Mid',
                'senior' => 'Senior',
                'lead' => 'Lead',
            ],
        ];

        foreach ($defaults as $type => $attributes) {
            $order = 1;

            foreach ($attributes as $value => $label) {
                if (is_int($value)) {
                    $value = $label;
                }

                $this->insertAttribute($type, $value, $label, $order++);
            }
        }

        foreach (['department', 'employment_type', 'work_mode', 'experience_level'] as $type) {
            DB::table('jobs')
                ->select($type)
                ->whereNotNull($type)
                ->distinct()
                ->orderBy($type)
                ->get()
                ->each(fn (object $job): null => $this->insertAttribute(
                    $type,
                    $job->{$type},
                    $this->labelFromValue($job->{$type}),
                    1000,
                ));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('job_attributes');
    }

    private function insertAttribute(string $type, string $value, string $label, int $displayOrder): null
    {
        DB::table('job_attributes')->updateOrInsert(
            [
                'type' => $type,
                'value' => $value,
            ],
            [
                'label' => $label,
                'display_order' => $displayOrder,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        );

        return null;
    }

    private function labelFromValue(string $value): string
    {
        return Str::of($value)->replace('_', ' ')->title()->toString();
    }
};
