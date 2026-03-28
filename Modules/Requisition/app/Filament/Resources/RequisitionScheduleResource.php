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
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\Requisition\Filament\Resources\RequisitionScheduleResource\Pages;
use Modules\Requisition\Models\RequisitionSchedule;

class RequisitionScheduleResource extends Resource
{
    protected static ?string $model = RequisitionSchedule::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path';

    protected static string|\UnitEnum|null $navigationGroup = 'Requisitions';

    protected static ?int $navigationSort = 9;

    protected static ?string $navigationLabel = 'Recurring';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Schedule Details')->schema([
                Grid::make(2)->schema([
                    TextInput::make('name')->required()->maxLength(255),
                    Select::make('template_id')
                        ->label('Template')
                        ->relationship('template', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                ]),
                Grid::make(2)->schema([
                    Select::make('requester_employee_id')
                        ->label('Requester')
                        ->relationship('requesterEmployee', 'full_name')
                        ->searchable()
                        ->preload()
                        ->nullable(),
                    Select::make('cost_centre_id')
                        ->label('Cost Centre')
                        ->relationship('costCentre', 'name')
                        ->searchable()
                        ->preload()
                        ->nullable(),
                ]),
                Grid::make(2)->schema([
                    Select::make('frequency')
                        ->options([
                            'daily'     => 'Daily',
                            'weekly'    => 'Weekly',
                            'biweekly'  => 'Bi-weekly',
                            'monthly'   => 'Monthly',
                            'quarterly' => 'Quarterly',
                        ])
                        ->required()
                        ->live(),
                    DatePicker::make('next_run_at')
                        ->label('First / Next Run Date')
                        ->required()
                        ->default(now()),
                ]),
                Select::make('day_of_week')
                    ->label('Day of Week')
                    ->options([
                        0 => 'Monday', 1 => 'Tuesday', 2 => 'Wednesday',
                        3 => 'Thursday', 4 => 'Friday', 5 => 'Saturday', 6 => 'Sunday',
                    ])
                    ->nullable()
                    ->visible(fn (\Filament\Schemas\Components\Component $component) =>
                        ($component->getLivewire()->data['frequency'] ?? '') === 'weekly'
                    ),
                TextInput::make('day_of_month')
                    ->label('Day of Month (1-28)')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(28)
                    ->nullable()
                    ->visible(fn (\Filament\Schemas\Components\Component $component) =>
                        in_array($component->getLivewire()->data['frequency'] ?? '', ['monthly', 'biweekly'])
                    ),
                Grid::make(2)->schema([
                    Toggle::make('auto_submit')->label('Auto-Submit After Creation')->default(false)->inline(false),
                    Toggle::make('is_active')->label('Active')->default(true)->inline(false),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('template.name')->label('Template'),
                TextColumn::make('frequency')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                TextColumn::make('next_run_at')->label('Next Run')->date(),
                TextColumn::make('last_run_at')->label('Last Run')->date()->placeholder('Never'),
                ToggleColumn::make('is_active')->label('Active'),
                TextColumn::make('auto_submit')
                    ->label('Auto-Submit')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No')
                    ->color(fn ($state) => $state ? 'success' : 'gray'),
            ])
            ->defaultSort('next_run_at', 'asc')
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListRequisitionSchedules::route('/'),
            'create' => Pages\CreateRequisitionSchedule::route('/create'),
            'edit'   => Pages\EditRequisitionSchedule::route('/{record}/edit'),
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