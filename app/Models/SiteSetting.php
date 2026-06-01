<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Throwable;

class SiteSetting extends Model
{
    protected $fillable = [
        'header_logo_url',
        'header_logo_path',
        'footer_logo_url',
        'footer_logo_path',
        'favicon_url',
        'favicon_path',
        'copyright_text',
    ];

    public static function current(): ?self
    {
        try {
            if (! Schema::hasTable('site_settings')) {
                return null;
            }

            return static::query()->first();
        } catch (Throwable) {
            return null;
        }
    }

    public function headerLogoUrl(): ?string
    {
        return $this->uploadedUrl('header_logo_path') ?? $this->header_logo_url;
    }

    public function footerLogoUrl(): ?string
    {
        return $this->uploadedUrl('footer_logo_path') ?? $this->footer_logo_url;
    }

    public function faviconUrl(): ?string
    {
        return $this->uploadedUrl('favicon_path') ?? $this->favicon_url;
    }

    protected function uploadedUrl(string $column): ?string
    {
        $path = $this->{$column};

        if (blank($path)) {
            return null;
        }

        return Storage::disk('public')->url($path);
    }
}
