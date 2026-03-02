<?php

namespace Modules\HR\Filament\Clusters;

use Filament\Clusters\Cluster;
use Filament\Support\Icons\Heroicon;

class HrPerformanceCluster extends Cluster
{
    protected static ?string $navigationLabel = 'Performance';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static string|\UnitEnum|null $navigationGroup = 'HR Manager';

    protected static ?int $navigationSort = 60;

    protected static ?string $slug = 'hr-performance';
}