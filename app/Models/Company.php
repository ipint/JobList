<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Company extends Model
{
    use HasFactory;

    public const DEFAULT_JOB_FIELD_SETTINGS = [
        'department' => true,
        'postcode' => true,
        'location_name' => true,
        'salary' => true,
        'application_url' => true,
        'application_email' => true,
        'closing_date' => true,
        'expires_at' => true,
        'experience_level' => true,
        'visa_sponsorship_available' => true,
        'right_to_work_required' => true,
    ];

    protected $fillable = [
        'name',
        'slug',
        'website',
        'overview',
        'logo_url',
        'logo_path',
        'job_field_settings',
        'is_active',
    ];

    protected $casts = [
        'job_field_settings' => 'array',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (Company $company): void {
            if (blank($company->slug) && filled($company->name)) {
                $company->slug = Str::slug($company->name);
            }
        });
    }

    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function enabledJobFields(): array
    {
        return array_replace(
            self::DEFAULT_JOB_FIELD_SETTINGS,
            $this->job_field_settings ?? [],
        );
    }
}
