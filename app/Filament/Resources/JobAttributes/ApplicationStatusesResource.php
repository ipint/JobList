<?php

namespace App\Filament\Resources\JobAttributes;

use App\Filament\Clusters\ApplicationAttributesCluster;
use App\Filament\Resources\JobAttributes\Pages\ApplicationStatuses\ListApplicationStatuses;
use App\Models\ApplicationStatus;
use Filament\Support\Icons\Heroicon;

class ApplicationStatusesResource extends BaseTypedJobAttributeResource
{
    protected static ?string $model = ApplicationStatus::class;

    protected static string $permissionSection = 'application_statuses';

    protected static ?string $slug = 'attributes/application-statuses';

    protected static ?string $navigationLabel = 'Status';

    protected static ?string $cluster = ApplicationAttributesCluster::class;

    protected static ?int $navigationSort = 1;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedSignal;

    protected static bool $supportsColor = true;

    public static function getPages(): array
    {
        return [
            'index' => ListApplicationStatuses::route('/'),
        ];
    }
}
