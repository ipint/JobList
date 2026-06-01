<?php

namespace App\Filament\Resources\Companies\Pages;

use App\Filament\Resources\Companies\CompanyResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCompany extends CreateRecord
{
    protected static string $resource = CompanyResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (! (auth()->user()?->canAccess('job_fields', 'edit') ?? false)) {
            unset($data['job_field_settings']);
        }

        return $data;
    }
}
