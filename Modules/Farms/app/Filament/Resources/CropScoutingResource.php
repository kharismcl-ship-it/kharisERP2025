<?php

namespace Modules\Farms\Filament\Resources;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
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
use Modules\Farms\Filament\Resources\CropScoutingResource\Pages;
use Modules\Farms\Models\CropScoutingRecord;

class CropScoutingResource extends Resource
{
    protected static ?string $model = CropScoutingRecord::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-magnifying-glass';

    protected static string|\UnitEnum|null $navigationGroup = 'Farms';

    protected static ?int $navigationSort = 6;

    protected static ?string $navigationLabel = 'Scouting';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Scouting Details')
                ->columns(2)
                ->schema([
                    Select::make('crop_cycle_id')
                        ->label('Crop Cycle')
                        ->relationship('cropCycle', 'crop_name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Select::make('farm_id')
                        ->label('Farm')
                        ->relationship('farm', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    DatePicker::make('scouting_date')->required(),
                    TextInput::make('scouted_by')->maxLength(255),

                    Select::make('finding_type')
                        ->options(array_combine(
                            CropScoutingRecord::FINDING_TYPES,
                            array_map('ucwords', array_map(fn ($v) => str_replace('_', ' ', $v), CropScoutingRecord::FINDING_TYPES))
                        ))
                        ->required(),

                    Select::make('severity')
                        ->options(array_combine(
                            CropScoutingRecord::SEVERITIES,
                            array_map('ucfirst', CropScoutingRecord::SEVERITIES)
                        ))
                        ->required(),

                    DatePicker::make('follow_up_date')->label('Follow-Up Date'),
                ]),

            Section::make('Findings & Action')
                ->schema([
                    Textarea::make('description')->required()->rows(3)->columnSpanFull(),
                    Textarea::make('recommended_action')->rows(2)->columnSpanFull(),
                ]),

            Section::make('Notes')
                ->collapsible()
                ->schema([Textarea::make('notes')->rows(2)->columnSpanFull()]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('finding_type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state)))
                    ->color(fn (string $state): string => match ($state) {
                        'pest', 'disease' => 'danger',
                        'normal'          => 'success',
                        default           => 'warning',
                    }),

                TextColumn::make('severity')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'critical' => 'danger',
                        'high'     => 'warning',
                        'medium'   => 'info',
                        'low'      => 'success',
                        default    => 'gray',
                    }),

                TextColumn::make('scouting_date')->date('d M Y')->sortable(),
                TextColumn::make('cropCycle.crop_name')->label('Crop'),
                TextColumn::make('farm.name')->label('Farm'),
                TextColumn::make('scouted_by')->placeholder('—'),

                TextColumn::make('follow_up_date')
                    ->date('d M Y')
                    ->label('Follow Up')
                    ->placeholder('—')
                    ->color(fn ($state, $record) =>
                        $state && now()->gt($state) && ! $record->resolved_at ? 'danger' : null
                    ),

                TextColumn::make('resolved_at')
                    ->label('Resolved')
                    ->dateTime('d M Y')
                    ->placeholder('Open'),
            ])
            ->filters([
                SelectFilter::make('finding_type')
                    ->options(array_combine(
                        CropScoutingRecord::FINDING_TYPES,
                        array_map('ucwords', array_map(fn ($v) => str_replace('_', ' ', $v), CropScoutingRecord::FINDING_TYPES))
                    )),
                SelectFilter::make('severity')
                    ->options(array_combine(
                        CropScoutingRecord::SEVERITIES,
                        array_map('ucfirst', CropScoutingRecord::SEVERITIES)
                    )),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('resolve')
                    ->label('Mark Resolved')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => ! $record->resolved_at)
                    ->action(fn ($record) => $record->update(['resolved_at' => now()])),
                DeleteAction::make(),
            ])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('scouting_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCropScoutingRecords::route('/'),
            'create' => Pages\CreateCropScoutingRecord::route('/create'),
            'view'   => Pages\ViewCropScoutingRecord::route('/{record}'),
            'edit'   => Pages\EditCropScoutingRecord::route('/{record}/edit'),
        ];
    }
}
