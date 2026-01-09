<?php

namespace Modules\Hostels\Filament\Resources;

use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\Hostels\Filament\Resources\HostelStaffRoleResource\Pages\CreateHostelStaffRole;
use Modules\Hostels\Filament\Resources\HostelStaffRoleResource\Pages\EditHostelStaffRole;
use Modules\Hostels\Filament\Resources\HostelStaffRoleResource\Pages\ListHostelStaffRoles;
use Modules\Hostels\Models\HostelStaffRole;

class HostelStaffRoleResource extends Resource
{
    protected static ?string $model = HostelStaffRole::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string|\UnitEnum|null $navigationGroup = 'Staff Management';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Role Information')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(120),
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(120)
                            ->unique(ignoreRecord: true),
                        Textarea::make('description')
                            ->maxLength(65535),
                        Toggle::make('is_active')
                            ->default(true),
                    ])->columns(2),

                Section::make('Salary Information')
                    ->schema([
                        TextInput::make('base_salary')
                            ->numeric()
                            ->prefix('GHS')
                            ->required(),
                        Select::make('salary_currency')
                            ->options([
                                'GHS' => 'Ghana Cedi',
                                'USD' => 'US Dollar',
                            ])
                            ->default('GHS')
                            ->required(),
                    ])->columns(2),

                Section::make('Permissions')
                    ->schema([
                        CheckboxList::make('permissions')
                            ->options([
                                'manage_rooms' => 'Manage Rooms',
                                'manage_bookings' => 'Manage Bookings',
                                'manage_staff' => 'Manage Staff',
                                'manage_housekeeping' => 'Manage Housekeeping',
                                'view_reports' => 'View Reports',
                                'process_payments' => 'Process Payments',
                                'manage_inventory' => 'Manage Inventory',
                                'manage_maintenance' => 'Manage Maintenance',
                            ])
                            ->columns(2)
                            ->gridDirection('row'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('base_salary')
                    ->money(fn ($record) => $record->salary_currency)
                    ->sortable(),
                IconColumn::make('is_active')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
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
            'index' => ListHostelStaffRoles::route('/'),
            'create' => CreateHostelStaffRole::route('/create'),
            'edit' => EditHostelStaffRole::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}
