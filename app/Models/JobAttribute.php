<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class JobAttribute extends Model
{
    public const TYPES = [
        'department' => 'Department',
        'employment_type' => 'Employment type',
        'work_mode' => 'Work mode',
        'experience_level' => 'Experience level',
        'application_status' => 'Application status',
        'application_flag' => 'Application flag',
    ];

    public const COLOR_TYPES = [
        'application_status',
        'application_flag',
    ];

    public const TYPE_MODEL_MAP = [
        'department' => Department::class,
        'employment_type' => EmploymentType::class,
        'work_mode' => WorkMode::class,
        'experience_level' => ExperienceLevel::class,
        'application_status' => ApplicationStatus::class,
        'application_flag' => ApplicationFlag::class,
    ];

    public const COLORS = [
        'gray' => 'Gray',
        'primary' => 'Blue',
        'info' => 'Light blue',
        'success' => 'Green',
        'warning' => 'Orange',
        'danger' => 'Red',
    ];

    protected $fillable = [
        'type',
        'label',
        'value',
        'color',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'display_order' => 'integer',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (JobAttribute $attribute): void {
            if (blank($attribute->value) && filled($attribute->label)) {
                $attribute->value = Str::slug($attribute->label, '_');
            }

            if (! static::typeSupportsColor($attribute->type)) {
                $attribute->color = null;
            } elseif (blank($attribute->color)) {
                $attribute->color = 'gray';
            }
        });
    }

    public static function typeSupportsColor(?string $type): bool
    {
        return filled($type) && in_array($type, static::COLOR_TYPES, true);
    }

    public function scopeForType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public static function optionsFor(string $type): array
    {
        $model = static::modelForType($type);

        if (! $model) {
            return [];
        }

        return $model::query()
            ->active()
            ->orderBy('display_order')
            ->orderBy('label')
            ->pluck('label', 'value')
            ->all();
    }

    public static function labelFor(?string $type, ?string $value): ?string
    {
        if (blank($type) || blank($value)) {
            return $value;
        }

        $model = static::modelForType($type);

        if (! $model) {
            return Str::of($value)->replace('_', ' ')->title()->toString();
        }

        return $model::query()
            ->where('value', $value)
            ->value('label') ?? Str::of($value)->replace('_', ' ')->title()->toString();
    }

    public static function colorFor(?string $type, ?string $value): string
    {
        if (blank($type) || blank($value)) {
            return 'gray';
        }

        $model = static::modelForType($type);

        if (! $model) {
            return 'gray';
        }

        $color = $model::query()
            ->where('value', $value)
            ->value('color');

        if (filled($color)) {
            return $color;
        }

        return match ($value) {
            'new' => 'info',
            'reviewing', 'interviewing' => 'warning',
            'shortlisted', 'offered', 'hired' => 'success',
            'rejected', 'withdrawn' => 'danger',
            default => 'gray',
        };
    }

    protected static function modelForType(?string $type): ?string
    {
        if (blank($type)) {
            return null;
        }

        return static::TYPE_MODEL_MAP[$type] ?? null;
    }
}
