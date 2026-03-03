<?php

namespace Modules\HR\Filament\Clusters;

use Filament\Clusters\Cluster;
use Filament\Support\Icons\Heroicon;

class HrSetupCluster extends Cluster
{
    protected static ?string $navigationLabel = 'HR Setup';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|\UnitEnum|null $navigationGroup = 'HR Manager';

    protected static ?int $navigationSort = 80;

    protected static ?string $slug = 'hr-setup';
}
