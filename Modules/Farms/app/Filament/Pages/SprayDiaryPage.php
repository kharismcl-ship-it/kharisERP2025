<?php

namespace Modules\Farms\Filament\Pages;

use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Modules\Farms\Models\CropInputApplication;
use Modules\Farms\Models\Farm;
use Modules\Farms\Models\FarmInputChemical;

class SprayDiaryPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'Farm Operations';

    protected static ?string $navigationLabel = 'Spray Diary';

    protected static ?int $navigationSort = 24;

    protected string $view = 'farms::filament.pages.spray-diary';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                CropInputApplication::query()
                    ->with(['cropCycle.farm', 'farmPlot', 'chemical', 'applicatorWorker'])
                    ->whereHas('cropCycle', fn (Builder $q) => $q->where('company_id', Filament::getTenant()?->id))
            )
            ->columns([
                TextColumn::make('application_date')->label('Date')->date()->sortable(),
                TextColumn::make('cropCycle.farm.name')->label('Farm')->searchable(),
                TextColumn::make('farmPlot.name')->label('Plot')->toggleable(),
                TextColumn::make('cropCycle.name')->label('Crop Cycle')->toggleable(),
                TextColumn::make('chemical.product_name')->label('Chemical Product')->searchable(),
                TextColumn::make('chemical.active_ingredient')->label('Active Ingredient')->toggleable(),
                TextColumn::make('quantity_used')->label('Rate Applied')->numeric(2),
                TextColumn::make('phi_days_remaining')
                    ->label('PHI Days Left')
                    ->getStateUsing(function (CropInputApplication $record): ?string {
                        if (! $record->chemical?->phi_days || ! $record->application_date) {
                            return null;
                        }
                        $phiEnd = \Carbon\Carbon::parse($record->application_date)->addDays($record->chemical->phi_days);
                        $daysLeft = (int) now()->diffInDays($phiEnd, false);

                        return $daysLeft >= 0 ? "{$daysLeft} days" : 'Elapsed';
                    })
                    ->badge()
                    ->color(fn (?string $state): string => match(true) {
                        $state === null           => 'gray',
                        str_starts_with($state, '-') || $state === 'Elapsed' => 'success',
                        (int) $state < 7          => 'danger',
                        (int) $state < 14         => 'warning',
                        default                   => 'info',
                    }),
                TextColumn::make('wind_speed_kmh')->label('Wind (km/h)')->numeric(1)->toggleable(),
                TextColumn::make('applicatorWorker.name')->label('Applicator')->toggleable(),
                IconColumn::make('phi_compliant')->label('PHI OK')->boolean()->toggleable(),
            ])
            ->filters([
                SelectFilter::make('farm')
                    ->label('Farm')
                    ->options(fn () => Farm::where('company_id', Filament::getTenant()?->id)->pluck('name', 'id'))
                    ->query(fn (Builder $query, array $data) => $data['value']
                        ? $query->whereHas('cropCycle', fn (Builder $q) => $q->where('farm_id', $data['value']))
                        : $query
                    ),
                SelectFilter::make('chemical_class')
                    ->label('Chemical Class')
                    ->options(array_combine(
                        FarmInputChemical::CHEMICAL_CLASSES,
                        FarmInputChemical::CHEMICAL_CLASSES
                    ))
                    ->query(fn (Builder $query, array $data) => $data['value']
                        ? $query->whereHas('chemical', fn (Builder $q) => $q->where('chemical_class', $data['value']))
                        : $query
                    ),
                Filter::make('date_range')
                    ->form([
                        DatePicker::make('from')->label('From'),
                        DatePicker::make('until')->label('Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'],  fn ($q, $v) => $q->whereDate('application_date', '>=', $v))
                            ->when($data['until'], fn ($q, $v) => $q->whereDate('application_date', '<=', $v));
                    }),
            ])
            ->defaultSort('application_date', 'desc');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_pdf')
                ->label('Export PDF')
                ->icon('heroicon-o-printer')
                ->url(fn () => '#')
                ->openUrlInNewTab(),
        ];
    }
}