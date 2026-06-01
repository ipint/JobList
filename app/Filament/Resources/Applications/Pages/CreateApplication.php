<?php

namespace App\Filament\Resources\Applications\Pages;

use App\Filament\Resources\Applications\ApplicationResource;
use App\Models\Job;
use Filament\Resources\Pages\CreateRecord;

class CreateApplication extends CreateRecord
{
    protected static string $resource = ApplicationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $companyId = Job::query()->whereKey($data['job_id'])->value('company_id');

        abort_unless(auth()->user()?->hasCompanyAccess($companyId), 403);

        $data['company_id'] = $companyId;

        return $data;
    }
}
