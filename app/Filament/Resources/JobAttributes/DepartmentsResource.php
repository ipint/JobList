<?php

namespace App\Filament\Resources\JobAttributes;

use App\Filament\Clusters\JobAttributesCluster;
use App\Filament\Resources\JobAttributes\Pages\Departments\ListDepartments;
use App\Models\Department;
use Filament\Support\Icons\Heroicon;

class DepartmentsResource extends BaseTypedJobAttributeResource
{
    protected static ?string $model = Department::class;

    protected static string $permissionSection = 'departments';

    protected static ?string $slug = 'attributes/departments';

    protected static ?string $navigationLabel = 'Department';

    protected static ?string $cluster = JobAttributesCluster::class;

    protected static ?int $navigationSort = 2;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    public static function getPages(): array
    {
        return [
            'index' => ListDepartments::route('/'),
        ];
    }
}
