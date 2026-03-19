<?php

namespace Database\Seeders;

use App\Models\Job;
use Illuminate\Database\Seeder;

class JobSeeder extends Seeder
{
    public function run(): void
    {
        Job::query()->delete();

        Job::factory()
            ->count(30)
            ->create();
    }
}
