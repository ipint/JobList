<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Application extends Model
{
    use HasFactory;

    public const STATUSES = [
        'new' => 'New',
        'reviewing' => 'Reviewing',
        'shortlisted' => 'Shortlisted',
        'interviewing' => 'Interviewing',
        'offered' => 'Offered',
        'hired' => 'Hired',
        'rejected' => 'Rejected',
        'withdrawn' => 'Withdrawn',
    ];

    public const LEGACY_FLAGS = [
        'reject' => '⚑ Reject',
        'progress' => 'P Previously Applied',
        'shortlist' => '⚑ Shortlist',
    ];

    public const LEGACY_FLAG_COLORS = [
        'reject' => 'danger',
        'progress' => 'primary',
        'shortlist' => 'success',
    ];

    protected $fillable = [
        'company_id',
        'job_id',
        'candidate_first_name',
        'candidate_last_name',
        'candidate_name',
        'candidate_email',
        'candidate_phone',
        'cv_url',
        'cv_path',
        'cover_letter',
        'cover_letter_path',
        'status',
        'flag',
        'source',
        'notes',
        'applied_at',
    ];

    protected $casts = [
        'applied_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (Application $application): void {
            if (filled($application->job_id)) {
                $application->company_id = Job::query()->whereKey($application->job_id)->value('company_id');
            }

            if (blank($application->applied_at)) {
                $application->applied_at = now();
            }

            if (blank($application->flag)) {
                $application->flag = null;
            }

            $first = trim((string) ($application->candidate_first_name ?? ''));
            $last = trim((string) ($application->candidate_last_name ?? ''));
            $fullName = trim($first . ' ' . $last);

            if ($fullName !== '') {
                $application->candidate_name = $fullName;
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(ApplicationNote::class);
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->canManageAllCompanies()) {
            return $query;
        }

        return $query->whereIn('company_id', $user->accessibleCompanyIds());
    }

    public static function flagOptions(): array
    {
        $options = JobAttribute::optionsFor('application_flag');

        return filled($options) ? $options : static::LEGACY_FLAGS;
    }

    public static function flagColors(): array
    {
        $configured = ApplicationFlag::query()
            ->active()
            ->pluck('color', 'value')
            ->filter()
            ->all();

        if (! empty($configured)) {
            return $configured;
        }

        return static::LEGACY_FLAG_COLORS;
    }
}
