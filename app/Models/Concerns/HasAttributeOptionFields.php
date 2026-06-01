<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

trait HasAttributeOptionFields
{
    protected static function bootHasAttributeOptionFields(): void
    {
        static::saving(function ($record): void {
            if (blank($record->value) && filled($record->label)) {
                $record->value = Str::slug($record->label, '_');
            }
        });
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}

