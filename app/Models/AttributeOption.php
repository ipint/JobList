<?php

namespace App\Models;

use App\Models\Concerns\HasAttributeOptionFields;
use Illuminate\Database\Eloquent\Model;

class AttributeOption extends Model
{
    use HasAttributeOptionFields;

    protected $fillable = [
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
}

