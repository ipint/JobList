<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\UkCounty;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class JobApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_only_public_jobs(): void
    {
        $county = $this->createCounty();

        $publishedJob = Job::factory()->create([
            'county_id' => $county->id,
            'status' => 'published',
            'published_at' => now()->subDay(),
            'expires_at' => now()->addWeek(),
        ]);

        Job::factory()->create([
            'county_id' => $county->id,
            'status' => 'draft',
            'published_at' => null,
        ]);

        Job::factory()->create([
            'county_id' => $county->id,
            'status' => 'published',
            'published_at' => now()->subDays(2),
            'expires_at' => now()->subMinute(),
        ]);

        $response = $this->getJson('/api/jobs');

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', $publishedJob->slug)
            ->assertJsonPath('data.0.location.county', $county->name);
    }

    public function test_it_filters_jobs_for_frontend_listing(): void
    {
        $county = $this->createCounty();

        $matchingJob = Job::factory()->create([
            'title' => 'Senior Laravel Developer',
            'department' => 'Technology',
            'employment_type' => 'full_time',
            'work_mode' => 'remote',
            'experience_level' => 'senior',
            'county_id' => $county->id,
            'status' => 'published',
            'published_at' => now()->subHour(),
            'expires_at' => now()->addWeek(),
            'is_featured' => true,
        ]);

        Job::factory()->create([
            'title' => 'Finance Analyst',
            'department' => 'Finance',
            'employment_type' => 'contract',
            'work_mode' => 'hybrid',
            'experience_level' => 'mid',
            'county_id' => $county->id,
            'status' => 'published',
            'published_at' => now()->subHour(),
            'expires_at' => now()->addWeek(),
            'is_featured' => false,
        ]);

        $response = $this->getJson('/api/jobs?search=Laravel&department=Technology&employment_type=full_time&work_mode=remote&experience_level=senior&county='.$county->slug.'&featured=1');

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', $matchingJob->slug);
    }

    public function test_it_returns_a_public_job_by_slug(): void
    {
        $county = $this->createCounty();

        $job = Job::factory()->create([
            'county_id' => $county->id,
            'status' => 'published',
            'published_at' => now()->subDay(),
            'expires_at' => now()->addWeek(),
        ]);

        $response = $this->getJson("/api/jobs/{$job->slug}");

        $response
            ->assertOk()
            ->assertJsonPath('data.slug', $job->slug)
            ->assertJsonPath('data.location.county_slug', $county->slug);
    }

    public function test_it_does_not_return_a_non_public_job_by_slug(): void
    {
        $county = $this->createCounty();

        $job = Job::factory()->create([
            'county_id' => $county->id,
            'status' => 'draft',
            'published_at' => null,
        ]);

        $this->getJson("/api/jobs/{$job->slug}")
            ->assertNotFound();
    }

    private function createCounty(): UkCounty
    {
        return UkCounty::query()->create([
            'name' => 'Greater London',
            'slug' => Str::slug('Greater London'),
            'nation' => 'England',
            'display_order' => 1,
            'is_active' => true,
        ]);
    }
}
