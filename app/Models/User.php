<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Notifications\Notifiable;

#[Fillable(['first_name', 'last_name', 'name', 'email', 'password', 'company_id', 'is_super_admin', 'role_id', 'accessible_sections'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    public const SECTION_OPTIONS = [
        'jobs' => 'Jobs',
        'companies' => 'Companies',
        'job_fields' => 'Job Fields',
        'applications' => 'Applications',
        'departments' => 'Department',
        'employment_types' => 'Employment Type',
        'work_modes' => 'Work Mode',
        'experience_levels' => 'Experience Level',
        'application_statuses' => 'Status',
        'application_flags' => 'Flags',
        'media' => 'File Manager',
        'site_settings' => 'Site Settings',
        'users' => 'Users',
        'roles' => 'Roles',
        'xml_feeds' => 'XML Feeds',
    ];

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_super_admin' => 'boolean',
            'accessible_sections' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (User $user): void {
            $first = trim((string) ($user->first_name ?? ''));
            $last = trim((string) ($user->last_name ?? ''));
            $fullName = trim($first . ' ' . $last);

            if ($fullName !== '') {
                $user->name = $fullName;
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class)->withTimestamps();
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function canManageAllCompanies(): bool
    {
        return $this->is_super_admin;
    }

    public function canAccessSection(string $section): bool
    {
        return $this->canAccess($section, 'view');
    }

    public function canAccess(string $section, string $action = 'view'): bool
    {
        if ($this->is_super_admin) {
            return true;
        }

        if ($this->role?->allows($section, $action)) {
            return true;
        }

        return $action === 'view' && in_array($section, $this->accessible_sections ?? [], true);
    }

    public function accessibleCompanyIds(): array
    {
        if ($this->canManageAllCompanies()) {
            return Company::query()->pluck('id')->all();
        }

        return $this->companies()->pluck('companies.id')->all();
    }

    public function hasCompanyAccess(?int $companyId): bool
    {
        if (! $companyId) {
            return false;
        }

        return $this->canManageAllCompanies() || in_array($companyId, $this->accessibleCompanyIds(), true);
    }
}
