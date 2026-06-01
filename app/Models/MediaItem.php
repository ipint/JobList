<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MediaItem extends Model
{
    protected $fillable = [
        'title',
        'alt_text',
        'disk',
        'path',
        'original_name',
        'mime_type',
        'size',
    ];

    protected $casts = [
        'size' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (MediaItem $mediaItem): void {
            if (blank($mediaItem->disk)) {
                $mediaItem->disk = 'public';
            }

            if (blank($mediaItem->path) || ! Storage::disk($mediaItem->disk)->exists($mediaItem->path)) {
                return;
            }

            $mediaItem->mime_type ??= Storage::disk($mediaItem->disk)->mimeType($mediaItem->path);
            $mediaItem->size ??= Storage::disk($mediaItem->disk)->size($mediaItem->path);
            $mediaItem->original_name ??= basename($mediaItem->path);
            $mediaItem->title ??= pathinfo($mediaItem->original_name, PATHINFO_FILENAME);
        });
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }
}
