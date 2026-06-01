<?php

namespace App\Filament\Resources\JobAttributes;

use App\Filament\Clusters\JobAttributesCluster;
use App\Filament\Resources\JobAttributes\Pages\ExperienceLevels\ListExperienceLevels;
use App\Models\ExperienceLevel;
use Filament\Support\Icons\Heroicon;

class ExperienceLevelsResource extends BaseTypedJobAttributeResource
{
    protected static ?string $model = ExperienceLevel::class;

    protected static string $permissionSection = 'experience_levels';

    protected static ?string $slug = 'attributes/experience-levels';

    protected static ?string $navigationLabel = 'Experience Level';

    protected static ?string $cluster = JobAttributesCluster::class;

    protected static ?int $navigationSort = 5;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    public static function getPages(): array
    {
        return [
            'index' => ListExperienceLevels::route('/'),
        ];
    }
}
