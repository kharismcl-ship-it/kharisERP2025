<?php

namespace Modules\Requisition\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\Requisition\Filament\Resources\RequisitionApprovalDelegationResource\Pages;
use Modules\Requisition\Models\RequisitionApprovalDelegation;

class RequisitionApprovalDelegationResource extends Resource
{
    protected static ?string $model = RequisitionApprovalDelegation::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static string|\UnitEnum|null $navigationGroup = 'Requisitions';

    protected static ?int $navigationSort = 13;

    protected static ?string $navigationLabel = 'Delegations';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Delegation Details')->schema([
                Grid::make(2)->schema([
                    Select::make('delegator_employee_id')
                        ->label('Delegating From (Delegator)')
                        ->relationship('delegator', 'full_name')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Select::make('delegate_employee_id')
                        ->label('Delegating To (Delegate)')
                        ->relationship('delegate', 'full_name')
                        ->searchable()
                        ->preload()
                        ->required(),
                ]),
                Grid::make(2)->schema([
                    DatePicker::make('starts_at')->label('Delegation Start')->required(),
                    DatePicker::make('ends_at')
                        ->label('Delegation End')
                        ->required()
                        ->afterOrEqual('starts_at'),
                ]),
                TextInput::make('reason')->label('Reason (e.g. Annual Leave)')->nullable()->maxLength(255)->columnSpanFull(),
                Toggle::make('is_active')->label('Active')->default(true)->inline(false),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('delegator.full_name')->label('Delegating From')->searchable(),
                TextColumn::make('delegate.full_name')->label('Delegating To')->searchable(),
                TextColumn::make('starts_at')->label('From')->date(),
                TextColumn::make('ends_at')->label('To')->date(),
                TextColumn::make('reason')->placeholder('—')->limit(30),
                IconColumn::make('is_active')->label('Active')->boolean(),
            ])
            ->defaultSort('starts_at', 'desc')
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListRequisitionApprovalDelegations::route('/'),
            'create' => Pages\CreateRequisitionApprovalDelegation::route('/create'),
            'edit'   => Pages\EditRequisitionApprovalDelegation::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query  = parent::getEloquentQuery();
        $tenant = filament()->getTenant();

        if ($tenant) {
            $query->where('company_id', $tenant->getKey());
        }

        return $query;
    }
}