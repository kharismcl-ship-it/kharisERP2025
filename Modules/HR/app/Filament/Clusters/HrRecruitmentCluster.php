<?php

namespace Modules\HR\Filament\Clusters;

use Filament\Clusters\Cluster;
use Filament\Support\Icons\Heroicon;

class HrRecruitmentCluster extends Cluster
{
    protected static ?string $navigationLabel = 'Recruitment';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static string|\UnitEnum|null $navigationGroup = 'HR Manager';

    protected static ?int $navigationSort = 50;

    protected static ?string $slug = 'hr-recruitment';
}