<?php

namespace Modules\HR\Filament\Clusters;

use Filament\Clusters\Cluster;
use Filament\Support\Icons\Heroicon;

class HrLearningCluster extends Cluster
{
    protected static ?string $navigationLabel = 'Learning & Dev';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static string|\UnitEnum|null $navigationGroup = 'HR Manager';

    protected static ?int $navigationSort = 65;

    protected static ?string $slug = 'hr-learning';
}