<?php

namespace Modules\Farms\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Facades\Filament;
use Modules\Farms\Filament\Resources\LivestockBreedingEventResource\Pages;
use Modules\Farms\Models\LivestockBreedingEvent;
use Modules\Farms\Models\LivestockBatch;

class LivestockBreedingEventResource extends Resource
{
    protected static ?string $model = LivestockBreedingEvent::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-heart';

    protected static string|\UnitEnum|null $navigationGroup = 'Livestock';

    protected static ?string $navigationLabel = 'Breeding Events';

    protected static ?int $navigationSort = 30;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Breeding Event')
                ->columns(2)
                ->schema([
                    Select::make('livestock_batch_id')
                        ->label('Livestock Batch')
                        ->options(fn () => LivestockBatch::where('company_id', Filament::getTenant()?->id)
                            ->where('status', 'active')
                            ->pluck('batch_reference', 'id'))
                        ->searchable()
                        ->required(),

                    Select::make('event_type')
                        ->options([
                            'mating'           => 'Mating',
                            'pregnancy_check'  => 'Pregnancy Check',
                            'parturition'      => 'Parturition (Birth)',
                            'weaning'          => 'Weaning',
                            'abortion'         => 'Abortion',
                            'stillbirth'       => 'Stillbirth',
                        ])
                        ->required()
                        ->live(),

                    DatePicker::make('event_date')->required()->default(now()),

                    Select::make('method')
                        ->options([
                            'natural'               => 'Natural',
                            'artificial_insemination' => 'Artificial Insemination',
                            'embryo_transfer'       => 'Embryo Transfer',
                        ])
                        ->nullable(),

                    TextInput::make('sire_description')->label('Sire ID / Description')->nullable()->maxLength(200),
                    TextInput::make('dam_description')->label('Dam Description')->nullable()->maxLength(200),

                    DatePicker::make('expected_parturition_date')->label('Expected Birth Date')->nullable(),
                    DatePicker::make('actual_parturition_date')->label('Actual Birth Date')->nullable(),

                    TextInput::make('offspring_count')->label('Offspring Born')->numeric()->integer()->nullable(),
                    TextInput::make('offspring_alive')->label('Offspring Alive')->numeric()->integer()->nullable(),
                    TextInput::make('conception_rate_pct')->label('Conception Rate (%)')->numeric()->step(0.01)->nullable(),
                ]),

            Section::make('Notes')
                ->collapsible()
                ->schema([
                    Textarea::make('notes')->rows(3)->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('batch.batch_reference')->label('Batch')->sortable()->searchable(),
                TextColumn::make('event_type')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'mating'          => 'info',
                        'parturition'     => 'success',
                        'abortion'        => 'danger',
                        'stillbirth'      => 'danger',
                        'pregnancy_check' => 'warning',
                        default           => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucwords(str_replace('_', ' ', $state))),
                TextColumn::make('event_date')->date()->sortable(),
                TextColumn::make('expected_parturition_date')->label('Expected Birth')->date()->placeholder('—'),
                TextColumn::make('offspring_count')->label('Born')->numeric(),
                TextColumn::make('conception_rate_pct')->label('Conception %')->numeric(2),
            ])
            ->filters([
                SelectFilter::make('livestock_batch_id')
                    ->label('Batch')
                    ->options(fn () => LivestockBatch::where('company_id', Filament::getTenant()?->id)->pluck('batch_reference', 'id')),
                SelectFilter::make('event_type')->options([
                    'mating'           => 'Mating',
                    'pregnancy_check'  => 'Pregnancy Check',
                    'parturition'      => 'Parturition',
                    'weaning'          => 'Weaning',
                    'abortion'         => 'Abortion',
                    'stillbirth'       => 'Stillbirth',
                ]),
            ])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('event_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListLivestockBreedingEvents::route('/'),
            'create' => Pages\CreateLivestockBreedingEvent::route('/create'),
            'edit'   => Pages\EditLivestockBreedingEvent::route('/{record}/edit'),
        ];
    }
}