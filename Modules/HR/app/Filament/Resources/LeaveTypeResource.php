<?php

namespace Modules\HR\Filament\Resources;

use Modules\HR\Filament\Clusters\HrSetupCluster;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\HR\Filament\Resources\LeaveTypeResource\Pages;
use Modules\HR\Models\LeaveType;

class LeaveTypeResource extends Resource
{
    protected static ?string $cluster = HrSetupCluster::class;
    protected static ?string $model = LeaveType::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocument;


    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('company_id')
                    ->relationship('company', 'name')
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('code')
                    ->label('Code')
                    ->required()
                    ->maxLength(50)
                    ->unique(ignoreRecord: true),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('max_days_per_year')
                    ->label('Max Days Per Year')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->default(0),
                Forms\Components\Toggle::make('requires_approval')
                    ->label('Requires Approval')
                    ->default(true),
                Forms\Components\Toggle::make('is_paid')
                    ->label('Is Paid Leave')
                    ->default(true),
                Forms\Components\Toggle::make('is_active')
                    ->label('Is Active')
                    ->default(true),
                Forms\Components\Toggle::make('has_accrual')
                    ->label('Has Accrual')
                    ->default(true)
                    ->reactive(),
                Forms\Components\TextInput::make('accrual_rate')
                    ->label('Accrual Rate (days/month)')
                    ->numeric()
                    ->minValue(0)
                    ->step(0.01)
                    ->default(1.67)
                    ->visible(fn (Get $get) => $get('has_accrual')),
                Forms\Components\Select::make('accrual_frequency')
                    ->label('Accrual Frequency')
                    ->options([
                        'monthly' => 'Monthly',
                        'quarterly' => 'Quarterly',
                        'annually' => 'Annually',
                    ])
                    ->default('monthly')
                    ->visible(fn (Get $get) => $get('has_accrual')),
                Forms\Components\TextInput::make('carryover_limit')
                    ->label('Carryover Limit (days)')
                    ->numeric()
                    ->minValue(0)
                    ->step(0.5)
                    ->default(0)
                    ->visible(fn (Get $get) => $get('has_accrual')),
                Forms\Components\TextInput::make('max_balance')
                    ->label('Maximum Balance Cap (days)')
                    ->numeric()
                    ->minValue(0)
                    ->step(0.5)
                    ->nullable()
                    ->visible(fn (Get $get) => $get('has_accrual')),
                Forms\Components\Toggle::make('pro_rata_enabled')
                    ->label('Enable Pro-Rata Calculations')
                    ->default(true)
                    ->visible(fn (Get $get) => $get('has_accrual')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('max_days_per_year')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('requires_approval')
                    ->boolean()
                    ->label('Approval'),
                Tables\Columns\IconColumn::make('is_paid')
                    ->boolean()
                    ->label('Paid'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
                Tables\Columns\IconColumn::make('has_accrual')
                    ->boolean()
                    ->label('Accrual'),
                Tables\Columns\TextColumn::make('accrual_rate')
                    ->numeric(decimalPlaces: 2)
                    ->label('Rate')
                    ->sortable(),
                Tables\Columns\TextColumn::make('accrual_frequency')
                    ->label('Frequency')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('company')
                    ->relationship('company', 'name'),
            ])
            ->actions([
                EditAction::make()
                    ->slideOver(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            \Modules\HR\Filament\Resources\LeaveTypeResource\RelationManagers\LeaveRequestsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeaveTypes::route('/'),
            'create' => Pages\CreateLeaveType::route('/create'),
            'view' => Pages\ViewLeaveType::route('/{record}'),
            'edit' => Pages\EditLeaveType::route('/{record}/edit'),
        ];
    }
}
