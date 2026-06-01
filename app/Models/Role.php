<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable(['name', 'slug', 'permissions', 'is_active'])]
class Role extends Model
{
    public const ACTIONS = ['view', 'create', 'edit', 'delete'];

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

    protected function casts(): array
    {
        return [
            'permissions' => 'array',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Role $role): void {
            if (blank($role->slug) && filled($role->name)) {
                $role->slug = Str::slug($role->name);
            }
        });
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public static function permissionOptions(): array
    {
        $options = [];

        foreach (static::SECTION_OPTIONS as $section => $label) {
            foreach (static::ACTIONS as $action) {
                $options["{$section}.{$action}"] = "{$label}: " . str($action)->ucfirst()->toString();
            }
        }

        return $options;
    }

    public function allows(string $section, string $action = 'view'): bool
    {
        if (! $this->is_active) {
            return false;
        }

        return in_array("{$section}.{$action}", $this->permissions ?? [], true);
    }
}
