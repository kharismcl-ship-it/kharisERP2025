<?php

namespace Modules\Requisition\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\Requisition\Filament\Resources\RequisitionResource\Pages;
use Modules\Requisition\Filament\Resources\RequisitionResource\RelationManagers;
use Modules\Requisition\Filament\Resources\RequisitionResource\Schemas\RequisitionForm;
use Modules\Requisition\Filament\Resources\RequisitionResource\Schemas\RequisitionInfolist;
use Modules\Requisition\Filament\Resources\RequisitionResource\Tables\RequisitionsTable;
use Modules\Requisition\Models\Requisition;

class RequisitionResource extends Resource
{
    protected static ?string $model = Requisition::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-inbox-arrow-down';

    protected static string|\UnitEnum|null $navigationGroup = 'Requisitions';

    protected static ?int $navigationSort = 1;

    /**
     * Cross-company visibility: show requests raised BY or targeted AT the current tenant.
     */
    public static function getEloquentQuery(): Builder
    {
        $query  = parent::getEloquentQuery();
        $tenant = filament()->getTenant();

        if ($tenant) {
            $tenantId = $tenant->getKey();
            $query->withoutGlobalScope(\App\Models\Scopes\TenantScope::class)
                ->where(fn (Builder $q) => $q
                    ->where('company_id', $tenantId)
                    ->orWhere('target_company_id', $tenantId)
                );
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return RequisitionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RequisitionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RequisitionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\RequisitionItemsRelationManager::class,
            RelationManagers\RequisitionApproversRelationManager::class,
            RelationManagers\RequisitionPartiesRelationManager::class,
            RelationManagers\RequisitionAttachmentsRelationManager::class,
            RelationManagers\RequisitionActivitiesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListRequisitions::route('/'),
            'create' => Pages\CreateRequisition::route('/create'),
            'view'   => Pages\ViewRequisition::route('/{record}'),
            'edit'   => Pages\EditRequisition::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['reference', 'title'];
    }
}