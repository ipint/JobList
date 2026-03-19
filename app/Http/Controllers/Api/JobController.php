<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\JobResource;
use App\Models\Job;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'county' => ['nullable', 'string', 'max:120'],
            'department' => ['nullable', 'string', 'max:120'],
            'employment_type' => ['nullable', 'string', 'max:50'],
            'work_mode' => ['nullable', 'string', 'max:50'],
            'experience_level' => ['nullable', 'string', 'max:50'],
            'featured' => ['nullable', 'boolean'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $jobs = Job::query()
            ->with('county')
            ->publiclyVisible()
            ->when($validated['search'] ?? null, function ($query, string $search) {
                $query->where(function ($innerQuery) use ($search): void {
                    $innerQuery
                        ->where('title', 'like', "%{$search}%")
                        ->orWhere('company_name', 'like', "%{$search}%")
                        ->orWhere('department', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%")
                        ->orWhere('location_name', 'like', "%{$search}%");
                });
            })
            ->when($validated['county'] ?? null, function ($query, string $county) {
                $query->whereHas('county', function ($countyQuery) use ($county): void {
                    $countyQuery
                        ->where('slug', $county)
                        ->orWhere('name', $county);
                });
            })
            ->when($validated['department'] ?? null, fn ($query, string $department) => $query->where('department', $department))
            ->when($validated['employment_type'] ?? null, fn ($query, string $employmentType) => $query->where('employment_type', $employmentType))
            ->when($validated['work_mode'] ?? null, fn ($query, string $workMode) => $query->where('work_mode', $workMode))
            ->when($validated['experience_level'] ?? null, fn ($query, string $experienceLevel) => $query->where('experience_level', $experienceLevel))
            ->when(array_key_exists('featured', $validated), fn ($query) => $query->where('is_featured', (bool) $validated['featured']))
            ->orderByDesc('is_featured')
            ->orderByDesc('published_at')
            ->paginate($validated['per_page'] ?? 12)
            ->withQueryString();

        return JobResource::collection($jobs);
    }

    public function show(Job $job): JobResource
    {
        abort_unless($job->isPubliclyVisible(), 404);

        $job->load('county');

        return new JobResource($job);
    }
}
