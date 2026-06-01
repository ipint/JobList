<?php

namespace App\Filament\Resources\JobAttributes;

use App\Filament\Clusters\JobAttributesCluster;
use App\Filament\Resources\JobAttributes\Pages\WorkModes\ListWorkModes;
use App\Models\WorkMode;
use Filament\Support\Icons\Heroicon;

class WorkModesResource extends BaseTypedJobAttributeResource
{
    protected static ?string $model = WorkMode::class;

    protected static string $permissionSection = 'work_modes';

    protected static ?string $slug = 'attributes/work-modes';

    protected static ?string $navigationLabel = 'Work Mode';

    protected static ?string $cluster = JobAttributesCluster::class;

    protected static ?int $navigationSort = 4;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeAlt;

    public static function getPages(): array
    {
        return [
            'index' => ListWorkModes::route('/'),
        ];
    }
}
