<?php

namespace Modules\Farms\Filament\Clusters;

use Filament\Clusters\Cluster;

class CropsCluster extends Cluster
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';

    protected static ?string $navigationLabel = 'Crops';

    protected static string|\UnitEnum|null $navigationGroup = 'Farms';

    protected static ?int $navigationSort = 10;

    protected static ?string $slug = 'farms-crops';
}
