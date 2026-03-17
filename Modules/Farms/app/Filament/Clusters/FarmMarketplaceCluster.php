<?php

namespace Modules\Farms\Filament\Clusters;

use Filament\Clusters\Cluster;

class FarmMarketplaceCluster extends Cluster
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationLabel = 'Marketplace';

    protected static string|\UnitEnum|null $navigationGroup = 'Farms';

    protected static ?int $navigationSort = 35;

    protected static ?string $slug = 'farms-marketplace';
}
