<?php

namespace Modules\Farms\Filament\Clusters;

use Filament\Clusters\Cluster;

class FarmFinanceCluster extends Cluster
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Finance';

    protected static string|\UnitEnum|null $navigationGroup = 'Farms';
}
