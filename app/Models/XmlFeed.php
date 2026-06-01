<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class XmlFeed extends Model
{
    public const XML_FIELD_OPTIONS = [
        'id' => 'ID',
        'company_id' => 'Company ID',
        'title' => 'Title',
        'slug' => 'Slug',
        'reference' => 'Reference',
        'company_name' => 'Company Name',
        'department' => 'Department',
        'description' => 'Description',
        'requirements' => 'Requirements',
        'benefits' => 'Benefits',
        'employment_type' => 'Employment Type',
        'work_mode' => 'Work Mode',
        'experience_level' => 'Experience Level',
        'status' => 'Status',
        'county_id' => 'County ID',
        'city' => 'City',
        'postcode' => 'Postcode',
        'location_name' => 'Location Name',
        'salary_min' => 'Salary Min',
        'salary_max' => 'Salary Max',
        'salary_currency' => 'Salary Currency',
        'salary_period' => 'Salary Period',
        'salary_text' => 'Salary Text',
        'is_salary_visible' => 'Is Salary Visible',
        'application_url' => 'Application URL',
        'application_email' => 'Application Email',
        'visa_sponsorship_available' => 'Visa Sponsorship Available',
        'right_to_work_required' => 'Right To Work Required',
        'closing_date' => 'Closing Date',
        'published_at' => 'Published At',
        'expires_at' => 'Expires At',
        'is_featured' => 'Is Featured',
        'created_at' => 'Created At',
        'updated_at' => 'Updated At',
    ];

    public const DEFAULT_XML_FIELDS = [
        'id',
        'title',
        'reference',
        'company_name',
        'department',
        'employment_type',
        'work_mode',
        'experience_level',
        'city',
        'postcode',
        'closing_date',
        'description',
        'application_url',
    ];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'departments',
        'employment_types',
        'work_modes',
        'experience_levels',
        'selected_fields',
        'is_active',
    ];

    protected $casts = [
        'departments' => 'array',
        'employment_types' => 'array',
        'work_modes' => 'array',
        'experience_levels' => 'array',
        'selected_fields' => 'array',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (XmlFeed $feed): void {
            if (blank($feed->slug) && filled($feed->name)) {
                $feed->slug = Str::slug($feed->name);
            }
        });
    }

    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'company_xml_feed')->withTimestamps();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public static function xmlFieldOptions(): array
    {
        return self::XML_FIELD_OPTIONS;
    }

    public static function defaultXmlFields(): array
    {
        return self::DEFAULT_XML_FIELDS;
    }
}
