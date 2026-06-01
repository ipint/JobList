<?php

namespace App\Filament\Resources\JobAttributes;

use App\Filament\Clusters\ApplicationAttributesCluster;
use App\Filament\Resources\JobAttributes\Pages\ApplicationFlags\ListApplicationFlags;
use App\Models\ApplicationFlag;
use Filament\Support\Icons\Heroicon;

class ApplicationFlagsResource extends BaseTypedJobAttributeResource
{
    protected static ?string $model = ApplicationFlag::class;

    protected static string $permissionSection = 'application_flags';

    protected static ?string $slug = 'attributes/application-flags';

    protected static ?string $navigationLabel = 'Flags';

    protected static ?string $cluster = ApplicationAttributesCluster::class;

    protected static ?int $navigationSort = 2;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedFlag;

    protected static bool $supportsColor = true;

    public static function getPages(): array
    {
        return [
            'index' => ListApplicationFlags::route('/'),
        ];
    }
}
