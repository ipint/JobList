<?php

namespace App\Filament\Resources\Jobs\Pages;

use App\Filament\Resources\Jobs\JobResource;
use App\Models\Company;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditJob extends EditRecord
{
    protected static string $resource = JobResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $user = auth()->user();

        if (! $user?->canManageAllCompanies()) {
            $companyId = $data['company_id'] ?? $this->record->company_id;

            abort_unless($user?->hasCompanyAccess($companyId), 403);

            $data['company_id'] = $companyId;
        }

        if (filled($data['company_id'] ?? null)) {
            $data['company_name'] = Company::query()->find($data['company_id'])?->name;
        }

        return $data;
    }
}
