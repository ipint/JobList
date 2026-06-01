<?php

namespace App\Filament\Resources\MediaItems\Pages;

use App\Filament\Resources\MediaItems\MediaItemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMediaItems extends ListRecords
{
    protected static string $resource = MediaItemResource::class;

    public function mount(): void
    {
        $this->redirect(route('filament.admin.pages.file-manager'), navigate: true);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
