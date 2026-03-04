<?php

namespace Modules\HR\Filament\Resources;

use Modules\HR\Filament\Clusters\HrRecordsCluster;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\HR\Filament\Resources\LeaveBalanceResource\Pages;
use Modules\HR\Models\LeaveBalance;

class LeaveBalanceResource extends Resource
{
    protected static ?string $cluster = HrRecordsCluster::class;
    protected static ?string $model = LeaveBalance::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?int $navigationSort = 21;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Balance Details')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('company_id')
                            ->relationship('company', 'name')
                            ,
                        Forms\Components\Select::make('employee_id')
                            ->relationship('employee', 'full_name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('leave_type_id')
                            ->relationship('leaveType', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('year')
                            ->required()
                            ->numeric()
                            ->default(now()->year),
                    ]),

                Section::make('Balance Figures')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('initial_balance')
                            ->label('Initial Balance')
                            ->required()
                            ->numeric()
                            ->step(0.5)
                            ->minValue(0)
                            ->live()
                            ->afterStateUpdated(function (Set $set, Get $get) {
                                $initial = (float) ($get('initial_balance') ?? 0);
                                $used = (float) ($get('used_balance') ?? 0);
                                $carriedOver = (float) ($get('carried_over') ?? 0);
                                $adjustments = (float) ($get('adjustments') ?? 0);
                                $set('current_balance', round($initial + $carriedOver + $adjustments - $used, 2));
                            }),
                        Forms\Components\TextInput::make('used_balance')
                            ->label('Used Balance')
                            ->required()
                            ->numeric()
                            ->step(0.5)
                            ->minValue(0)
                            ->live()
                            ->afterStateUpdated(function (Set $set, Get $get) {
                                $initial = (float) ($get('initial_balance') ?? 0);
                                $used = (float) ($get('used_balance') ?? 0);
                                $carriedOver = (float) ($get('carried_over') ?? 0);
                                $adjustments = (float) ($get('adjustments') ?? 0);
                                $set('current_balance', round($initial + $carriedOver + $adjustments - $used, 2));
                            }),
                        Forms\Components\TextInput::make('carried_over')
                            ->label('Carried Over')
                            ->required()
                            ->numeric()
                            ->step(0.5)
                            ->minValue(0)
                            ->live()
                            ->afterStateUpdated(function (Set $set, Get $get) {
                                $initial = (float) ($get('initial_balance') ?? 0);
                                $used = (float) ($get('used_balance') ?? 0);
                                $carriedOver = (float) ($get('carried_over') ?? 0);
                                $adjustments = (float) ($get('adjustments') ?? 0);
                                $set('current_balance', round($initial + $carriedOver + $adjustments - $used, 2));
                            }),
                        Forms\Components\TextInput::make('adjustments')
                            ->label('Adjustments')
                            ->required()
                            ->numeric()
                            ->step(0.5)
                            ->live()
                            ->afterStateUpdated(function (Set $set, Get $get) {
                                $initial = (float) ($get('initial_balance') ?? 0);
                                $used = (float) ($get('used_balance') ?? 0);
                                $carriedOver = (float) ($get('carried_over') ?? 0);
                                $adjustments = (float) ($get('adjustments') ?? 0);
                                $set('current_balance', round($initial + $carriedOver + $adjustments - $used, 2));
                            }),
                        Forms\Components\TextInput::make('current_balance')
                            ->label('Current Balance')
                            ->required()
                            ->numeric()
                            ->step(0.5)
                            ->readOnly()
                            ->helperText('Auto-calculated: Initial + Carried Over + Adjustments − Used'),
                    ]),

                Section::make('Notes')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('employee.full_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('leaveType.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('year')
                    ->sortable(),
                Tables\Columns\TextColumn::make('initial_balance')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('used_balance')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('current_balance')
                    ->numeric()
                    ->sortable()
                    ->color(fn ($record) => match (true) {
                        $record->current_balance <= 0 => 'danger',
                        $record->current_balance <= 3 => 'warning',
                        default => 'success',
                    }),
                Tables\Columns\TextColumn::make('carried_over')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('adjustments')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_calculated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Filters\SelectFilter::make('employee')
                    ->relationship('employee', 'full_name'),
                Tables\Filters\SelectFilter::make('leave_type')
                    ->relationship('leaveType', 'name'),
                Tables\Filters\SelectFilter::make('year')
                    ->options(fn () => array_combine(
                        range(now()->year - 5, now()->year + 1),
                        range(now()->year - 5, now()->year + 1)
                    )),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeaveBalances::route('/'),
            'create' => Pages\CreateLeaveBalance::route('/create'),
            'view' => Pages\ViewLeaveBalance::route('/{record}'),
            'edit' => Pages\EditLeaveBalance::route('/{record}/edit'),
        ];
    }
}
