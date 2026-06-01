<?php

namespace App\Filament\Resources\JobAttributes\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Schema;

abstract class BaseListTypedJobAttributes extends ListRecords
{
    public function form(Schema $schema): Schema
    {
        return parent::form($schema->columns(1));
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->slideOver(),
        ];
    }
}
