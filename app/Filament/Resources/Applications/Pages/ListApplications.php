<?php

namespace App\Filament\Resources\Applications\Pages;

use App\Filament\Resources\Applications\ApplicationResource;
use App\Models\Application;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListApplications extends ListRecords
{
    protected static string $resource = ApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(fn (): bool => auth()->user()?->canAccess('applications', 'create') ?? false),
        ];
    }

    public function setFlag(int $recordId, ?string $flag): void
    {
        $user = auth()->user();

        if (! $user) {
            return;
        }

        $application = Application::query()
            ->visibleTo($user)
            ->find($recordId);

        if (! $application) {
            return;
        }

        if ($flag !== null && ! array_key_exists($flag, Application::flagOptions())) {
            return;
        }

        $application->update(['flag' => $flag]);
    }
}
