<?php

namespace Modules\HR\Filament\Resources;

use Filament\Actions\BulkActionGroup;
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
use Modules\HR\Filament\Resources\HostelStaffAssignmentResource\Pages;
use Modules\HR\Models\HostelStaffAssignment;

class HostelStaffAssignmentResource extends Resource
{
    protected static ?string $model = HostelStaffAssignment::class;

    protected static ?string $navigationLabel = 'Hostel Staff Assignments';

    protected static ?string $modelLabel = 'Hostel Staff Assignment';

    protected static ?string $pluralModelLabel = 'Hostel Staff Assignments';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedHomeModern;

    protected static string|\UnitEnum|null $navigationGroup = 'Core HR';

    protected static ?int $navigationSort = 15;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Assignment Details')
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
                        Forms\Components\Select::make('hostel_id')
                            ->relationship('hostel', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('role')
                            ->maxLength(255),
                    ]),

                Section::make('Dates')
                    ->columns(2)
                    ->schema([
                        Forms\Components\DateTimePicker::make('assigned_at'),
                        Forms\Components\DateTimePicker::make('expires_at'),
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
                Tables\Columns\TextColumn::make('hostel.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('role')
                    ->searchable(),
                Tables\Columns\TextColumn::make('assigned_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expires_at')
                    ->dateTime()
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
                Tables\Filters\SelectFilter::make('employee')
                    ->relationship('employee', 'full_name'),
                Tables\Filters\SelectFilter::make('hostel')
                    ->relationship('hostel', 'name'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
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
            'index'  => Pages\ListHostelStaffAssignments::route('/'),
            'create' => Pages\CreateHostelStaffAssignment::route('/create'),
            'view'   => Pages\ViewHostelStaffAssignment::route('/{record}'),
            'edit'   => Pages\EditHostelStaffAssignment::route('/{record}/edit'),
        ];
    }
}
