<?php

namespace Modules\HR\Filament\Clusters;

use Filament\Clusters\Cluster;
use Filament\Support\Icons\Heroicon;

class HrRelationsCluster extends Cluster
{
    protected static ?string $navigationLabel = 'Employee Relations';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedScale;

    protected static string|\UnitEnum|null $navigationGroup = 'HR Manager';

    protected static ?int $navigationSort = 70;

    protected static ?string $slug = 'hr-relations';
}