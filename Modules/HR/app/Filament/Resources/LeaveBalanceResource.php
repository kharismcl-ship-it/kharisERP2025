<?php

namespace Modules\HR\Filament\Resources;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\HR\Filament\Resources\LeaveBalanceResource\Pages;
use Modules\HR\Models\LeaveBalance;

class LeaveBalanceResource extends Resource
{
    protected static ?string $model = LeaveBalance::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static string|\UnitEnum|null $navigationGroup = 'Leave';

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
                            ->required(),
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
                            ->minValue(0),
                        Forms\Components\TextInput::make('used_balance')
                            ->label('Used Balance')
                            ->required()
                            ->numeric()
                            ->step(0.5)
                            ->minValue(0),
                        Forms\Components\TextInput::make('current_balance')
                            ->label('Current Balance')
                            ->required()
                            ->numeric()
                            ->step(0.5)
                            ->readOnly(),
                        Forms\Components\TextInput::make('carried_over')
                            ->label('Carried Over')
                            ->required()
                            ->numeric()
                            ->step(0.5)
                            ->minValue(0),
                        Forms\Components\TextInput::make('adjustments')
                            ->label('Adjustments')
                            ->required()
                            ->numeric()
                            ->step(0.5),
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
                    ->color(fn ($record) => $record->current_balance < 0 ? 'danger' : 'success'),
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
