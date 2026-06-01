<?php

namespace App\Filament\Resources\JobAttributes;

use App\Filament\Clusters\JobAttributesCluster;
use App\Filament\Resources\JobAttributes\Pages\EmploymentTypes\ListEmploymentTypes;
use App\Models\EmploymentType;
use Filament\Support\Icons\Heroicon;

class EmploymentTypesResource extends BaseTypedJobAttributeResource
{
    protected static ?string $model = EmploymentType::class;

    protected static string $permissionSection = 'employment_types';

    protected static ?string $slug = 'attributes/employment-types';

    protected static ?string $navigationLabel = 'Employment Type';

    protected static ?string $cluster = JobAttributesCluster::class;

    protected static ?int $navigationSort = 3;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedIdentification;

    public static function getPages(): array
    {
        return [
            'index' => ListEmploymentTypes::route('/'),
        ];
    }
}
