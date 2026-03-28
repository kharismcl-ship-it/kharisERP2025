<?php

namespace Modules\HR\Filament\Clusters;

use Filament\Clusters\Cluster;
use Filament\Support\Icons\Heroicon;

class HrSafetyCluster extends Cluster
{
    protected static ?string $navigationLabel = 'Health & Safety';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static string|\UnitEnum|null $navigationGroup = 'HR Manager';

    protected static ?int $navigationSort = 70;

    protected static ?string $slug = 'hr-safety';
}