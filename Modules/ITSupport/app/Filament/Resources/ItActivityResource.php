<?php

namespace Modules\ITSupport\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Modules\ITSupport\Filament\Resources\ItActivityResource\Pages;
use Modules\ITSupport\Models\ItActivity;

class ItActivityResource extends Resource
{
    protected static ?string $model = ItActivity::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-list-bullet';

    protected static string|\UnitEnum|null $navigationGroup = 'IT Support';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'IT Activities';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Activity Details')->schema([
                Grid::make(2)->schema([
                    Select::make('company_id')
                        ->label('Company')
                        ->relationship('company', 'name')
                        ->searchable()
                        ->preload()
                        ,
                    Select::make('performed_by_employee_id')
                        ->label('Performed By')
                        ->relationship('performedByEmployee', 'full_name')
                        ->searchable()
                        ->preload()
                        ->nullable(),
                ]),
                Grid::make(2)->schema([
                    Select::make('activity_type')
                        ->options(ItActivity::ACTIVITY_TYPES)
                        ->required(),
                    TextInput::make('title')->required()->maxLength(255),
                ]),
                Textarea::make('description')->required()->rows(3)->columnSpanFull(),
                Textarea::make('affected_systems')->label('Affected Systems')->rows(2)->columnSpanFull(),
            ]),

            Section::make('Schedule & Status')->schema([
                Grid::make(2)->schema([
                    DateTimePicker::make('scheduled_at')->nullable(),
                    DateTimePicker::make('completed_at')->nullable(),
                ]),
                Grid::make(2)->schema([
                    Select::make('status')
                        ->options(ItActivity::STATUSES)
                        ->default('planned')
                        ->required(),
                ]),
                Textarea::make('notes')->rows(2)->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable()->sortable()->limit(40),
                TextColumn::make('activity_type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ItActivity::ACTIVITY_TYPES[$state] ?? $state),
                TextColumn::make('performedByEmployee.full_name')->label('Performed By'),
                TextColumn::make('scheduled_at')->dateTime()->sortable(),
                TextColumn::make('completed_at')->dateTime()->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'planned'     => 'info',
                        'in_progress' => 'warning',
                        'completed'   => 'success',
                        'cancelled'   => 'danger',
                        default       => 'gray',
                    }),
            ])
            ->defaultSort('scheduled_at', 'desc')
            ->filters([
                SelectFilter::make('status')->options(ItActivity::STATUSES),
                SelectFilter::make('activity_type')->options(ItActivity::ACTIVITY_TYPES),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListItActivities::route('/'),
            'create' => Pages\CreateItActivity::route('/create'),
            'edit'   => Pages\EditItActivity::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title'];
    }
}
