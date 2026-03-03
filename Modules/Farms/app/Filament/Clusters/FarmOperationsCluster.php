<?php

namespace Modules\Farms\Filament\Clusters;

use Filament\Clusters\Cluster;

class FarmOperationsCluster extends Cluster
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $navigationLabel = 'Operations';

    protected static string|\UnitEnum|null $navigationGroup = 'Farms';

    protected static ?int $navigationSort = 30;

    protected static ?string $slug = 'farms-operations';
}
