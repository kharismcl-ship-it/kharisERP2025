<?php

namespace Modules\Farms\Filament\Clusters;

use Filament\Clusters\Cluster;

class LivestockCluster extends Cluster
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-heart';

    protected static ?string $navigationLabel = 'Livestock';

    protected static string|\UnitEnum|null $navigationGroup = 'Farms';

    protected static ?int $navigationSort = 20;

    protected static ?string $slug = 'farms-livestock';
}
