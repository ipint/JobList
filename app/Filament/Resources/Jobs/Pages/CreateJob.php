<?php

namespace App\Filament\Resources\Jobs\Pages;

use App\Filament\Resources\Jobs\JobResource;
use App\Models\Company;
use Filament\Resources\Pages\CreateRecord;

class CreateJob extends CreateRecord
{
    protected static string $resource = JobResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();

        if (! $user?->canManageAllCompanies()) {
            $companyId = $data['company_id'] ?? ($user?->accessibleCompanyIds()[0] ?? null);

            abort_unless($user?->hasCompanyAccess($companyId), 403);

            $data['company_id'] = $companyId;
            $data['company_name'] = Company::query()->find($companyId)?->name;
        } elseif (filled($data['company_id'] ?? null)) {
            $data['company_name'] = Company::query()->find($data['company_id'])?->name;
        }

        return $data;
    }
}
