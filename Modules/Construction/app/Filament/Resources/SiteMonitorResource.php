<?php

namespace Modules\Construction\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Modules\Construction\Filament\Resources\SiteMonitorResource\Pages;
use Modules\Construction\Models\ConstructionProject;
use Modules\Construction\Models\SiteMonitor;
use Modules\HR\Models\Employee;

class SiteMonitorResource extends Resource
{
    protected static ?string $model = SiteMonitor::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-eye';

    protected static string|\UnitEnum|null $navigationGroup = 'Construction';

    protected static ?int $navigationSort = 9;

    protected static ?string $navigationLabel = 'Monitoring Team';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Monitor Details')->schema([
                Grid::make(2)->schema([
                    Select::make('construction_project_id')
                        ->label('Project')
                        ->options(fn () => ConstructionProject::pluck('name', 'id')->toArray())
                        ->searchable()
                        ->required(),
                    Select::make('monitor_type')
                        ->options([
                            'internal'   => 'Internal',
                            'external'   => 'External',
                            'consultant' => 'Consultant',
                        ])
                        ->default('internal')
                        ->required()
                        ->live(),
                ]),
                Select::make('employee_id')
                    ->label('Linked Employee')
                    ->options(fn () => Employee::all()->pluck('full_name', 'id')->toArray())
                    ->searchable()
                    ->nullable()
                    ->visible(fn (Get $get) => $get('monitor_type') === 'internal'),
                Grid::make(2)->schema([
                    TextInput::make('name')->required()->maxLength(255),
                    TextInput::make('email')->email()->maxLength(255),
                ]),
                Grid::make(2)->schema([
                    TextInput::make('phone')->tel()->maxLength(20),
                    Select::make('role')
                        ->options([
                            'site_engineer'        => 'Site Engineer',
                            'quality_inspector'    => 'Quality Inspector',
                            'safety_officer'       => 'Safety Officer',
                            'independent_monitor'  => 'Independent Monitor',
                            'other'                => 'Other',
                        ])
                        ->required(),
                ]),
                Grid::make(2)->schema([
                    DatePicker::make('appointed_date')->label('Appointed Date'),
                    Toggle::make('is_active')->label('Active')->default(true),
                ]),
                Textarea::make('notes')->rows(3)->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('project.name')->label('Project')->searchable(),
                TextColumn::make('monitor_type')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'internal'   => 'info',
                        'external'   => 'warning',
                        'consultant' => 'success',
                        default      => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                TextColumn::make('role')->badge()
                    ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state))),
                TextColumn::make('appointed_date')->date()->placeholder('—'),
                IconColumn::make('is_active')->label('Active')->boolean(),
            ])
            ->filters([
                SelectFilter::make('monitor_type')
                    ->options([
                        'internal'   => 'Internal',
                        'external'   => 'External',
                        'consultant' => 'Consultant',
                    ]),
                SelectFilter::make('construction_project_id')
                    ->label('Project')
                    ->relationship('project', 'name'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ])
            ->defaultSort('name');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSiteMonitors::route('/'),
            'create' => Pages\CreateSiteMonitor::route('/create'),
            'view'   => Pages\ViewSiteMonitor::route('/{record}'),
            'edit'   => Pages\EditSiteMonitor::route('/{record}/edit'),
        ];
    }
}
