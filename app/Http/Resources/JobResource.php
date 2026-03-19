<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'reference' => $this->reference,
            'company_name' => $this->company_name,
            'department' => $this->department,
            'description' => $this->description,
            'requirements' => $this->requirements,
            'benefits' => $this->benefits,
            'employment_type' => $this->employment_type,
            'work_mode' => $this->work_mode,
            'experience_level' => $this->experience_level,
            'status' => $this->status,
            'location' => [
                'county' => $this->county?->name,
                'county_slug' => $this->county?->slug,
                'city' => $this->city,
                'postcode' => $this->postcode,
                'location_name' => $this->location_name,
            ],
            'salary' => [
                'min' => $this->salary_min,
                'max' => $this->salary_max,
                'currency' => $this->salary_currency,
                'period' => $this->salary_period,
                'text' => $this->salary_text,
                'is_visible' => $this->is_salary_visible,
            ],
            'application' => [
                'url' => $this->application_url,
                'email' => $this->application_email,
            ],
            'visa_sponsorship_available' => $this->visa_sponsorship_available,
            'right_to_work_required' => $this->right_to_work_required,
            'closing_date' => $this->closing_date?->toDateString(),
            'published_at' => $this->published_at?->toAtomString(),
            'expires_at' => $this->expires_at?->toAtomString(),
            'is_featured' => $this->is_featured,
            'created_at' => $this->created_at?->toAtomString(),
            'updated_at' => $this->updated_at?->toAtomString(),
        ];
    }
}
