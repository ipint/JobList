<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UkCounty extends Model
{
    use HasFactory;

    protected $table = 'uk_counties';

    protected $fillable = [
        'name',
        'slug',
        'nation',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class, 'county_id');
    }
}
