<?php

namespace App\Filament\Clusters;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ApplicationAttributesCluster extends Cluster
{
    protected static ?string $navigationLabel = 'Application Attributes';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedFunnel;

    protected static string | UnitEnum | null $navigationGroup = 'Recruitment';

    protected static ?int $navigationSort = 5;
}

