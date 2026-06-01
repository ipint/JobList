<?php

namespace App\Filament\Resources\Applications\Pages;

use App\Filament\Resources\Applications\ApplicationResource;
use App\Models\Job;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditApplication extends EditRecord
{
    protected static string $resource = ApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $companyId = Job::query()->whereKey($data['job_id'])->value('company_id');

        abort_unless(auth()->user()?->hasCompanyAccess($companyId), 403);

        $data['company_id'] = $companyId;

        return $data;
    }
}
