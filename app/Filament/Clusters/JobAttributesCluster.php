<?php

namespace App\Filament\Clusters;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class JobAttributesCluster extends Cluster
{
    protected static ?string $navigationLabel = 'Job Attributes';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedAdjustmentsHorizontal;

    protected static string | UnitEnum | null $navigationGroup = 'Recruitment';

    protected static ?int $navigationSort = 2;
}

