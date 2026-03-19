<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'reference',
        'company_name',
        'department',
        'description',
        'requirements',
        'benefits',
        'employment_type',
        'work_mode',
        'experience_level',
        'status',
        'county_id',
        'city',
        'postcode',
        'location_name',
        'salary_min',
        'salary_max',
        'salary_currency',
        'salary_period',
        'salary_text',
        'is_salary_visible',
        'application_url',
        'application_email',
        'visa_sponsorship_available',
        'right_to_work_required',
        'closing_date',
        'published_at',
        'expires_at',
        'is_featured',
    ];

    protected $casts = [
        'salary_min' => 'decimal:2',
        'salary_max' => 'decimal:2',
        'is_salary_visible' => 'boolean',
        'visa_sponsorship_available' => 'boolean',
        'right_to_work_required' => 'boolean',
        'is_featured' => 'boolean',
        'closing_date' => 'date',
        'published_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (Job $job): void {
            if (blank($job->slug) && filled($job->title)) {
                $job->slug = static::generateUniqueSlug($job->title, $job->getKey());
            }
        });
    }

    public function county(): BelongsTo
    {
        return $this->belongsTo(UkCounty::class, 'county_id');
    }

    protected static function generateUniqueSlug(string $title, mixed $ignoreId = null): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $counter = 2;

        while (static::query()
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
