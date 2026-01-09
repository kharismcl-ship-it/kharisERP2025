<?php

namespace Modules\Hostels\Filament\Resources;

use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\Hostels\Filament\Resources\HostelStaffResource\Pages\CreateHostelStaff;
use Modules\Hostels\Filament\Resources\HostelStaffResource\Pages\EditHostelStaff;
use Modules\Hostels\Filament\Resources\HostelStaffResource\Pages\ListHostelStaffs;
use Modules\Hostels\Models\HostelStaffRoleAssignment;

class HostelStaffResource extends Resource
{
    protected static ?string $model = HostelStaffRoleAssignment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static string|\UnitEnum|null $navigationGroup = 'Staff Management';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Staff Assignment')
                    ->schema([
                        Select::make('hostel_id')
                            ->relationship('hostel', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Select::make('employee_id')
                            ->relationship('employee', 'full_name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Select::make('role_id')
                            ->relationship('role', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                    ])->columns(3),

                Section::make('Assignment Details')
                    ->schema([
                        DatePicker::make('start_date')
                            ->required()
                            ->default(now()),
                        DatePicker::make('end_date')
                            ->nullable(),
                        Toggle::make('is_primary')
                            ->default(false),
                        Textarea::make('assignment_reason')
                            ->maxLength(65535),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('hostel.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('employee.full_name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('role.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                IconColumn::make('is_primary')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
            'index' => ListHostelStaffs::route('/'),
            'create' => CreateHostelStaff::route('/create'),
            'edit' => EditHostelStaff::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }
}
