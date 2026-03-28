<?php

namespace Modules\ProcurementInventory\Filament\Resources;

use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\ProcurementInventory\Filament\Resources\ProcurementApprovalRuleResource\Pages;
use Modules\ProcurementInventory\Models\ProcurementApprovalRule;

class ProcurementApprovalRuleResource extends Resource
{
    protected static ?string $model = ProcurementApprovalRule::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static string|\UnitEnum|null $navigationGroup = 'Procurement';

    protected static ?string $navigationLabel = 'Approval Rules';

    protected static ?int $navigationSort = 8;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Rule Details')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\Select::make('approver_user_id')
                        ->label('Approver')
                        ->options(User::orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->nullable(),

                    Forms\Components\TextInput::make('min_amount')
                        ->label('Min Amount (GHS)')
                        ->numeric()
                        ->prefix('GHS')
                        ->step(0.01)
                        ->nullable()
                        ->helperText('Trigger if PO total ≥ this value'),

                    Forms\Components\TextInput::make('max_amount')
                        ->label('Max Amount (GHS)')
                        ->numeric()
                        ->prefix('GHS')
                        ->step(0.01)
                        ->nullable()
                        ->helperText('Trigger if PO total < this value (leave blank for no upper limit)'),

                    Forms\Components\TextInput::make('sort_order')
                        ->numeric()
                        ->default(0)
                        ->label('Sort Order'),

                    Forms\Components\Toggle::make('is_active')
                        ->default(true)
                        ->label('Active'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('min_amount')
                    ->money('GHS')
                    ->placeholder('—')
                    ->label('Min'),

                Tables\Columns\TextColumn::make('max_amount')
                    ->money('GHS')
                    ->placeholder('—')
                    ->label('Max'),

                Tables\Columns\TextColumn::make('approver.name')
                    ->label('Approver')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProcurementApprovalRules::route('/'),
            'create' => Pages\CreateProcurementApprovalRule::route('/create'),
            'edit'   => Pages\EditProcurementApprovalRule::route('/{record}/edit'),
        ];
    }
}