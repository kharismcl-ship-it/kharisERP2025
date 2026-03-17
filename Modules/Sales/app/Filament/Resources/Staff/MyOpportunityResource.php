<?php

namespace Modules\Sales\Filament\Resources\Staff;

use Filament\Facades\Filament;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\Sales\Models\SalesOpportunity;
use Modules\Sales\Filament\Resources\Staff\MyOpportunityResource\Schemas\OpportunityInfolist;
use Modules\Sales\Filament\Resources\Staff\MyOpportunityResource\Tables\OpportunitiesTable;

class MyOpportunityResource extends StaffGatedResource
{
    protected static ?string $model = SalesOpportunity::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?string $navigationLabel = 'My Opportunities';

    protected static string|\UnitEnum|null $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 50;

    protected static ?string $slug = 'my-opportunities';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('assigned_to', auth()->id())
            ->where('company_id', Filament::getTenant()?->id);
    }

    public static function infolist(Schema $schema): Schema
    {
        return OpportunityInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OpportunitiesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => \Modules\Sales\Filament\Resources\Staff\MyOpportunityResource\Pages\ListMyOpportunities::route('/'),
            'view'  => \Modules\Sales\Filament\Resources\Staff\MyOpportunityResource\Pages\ViewMyOpportunity::route('/{record}'),
        ];
    }
}
