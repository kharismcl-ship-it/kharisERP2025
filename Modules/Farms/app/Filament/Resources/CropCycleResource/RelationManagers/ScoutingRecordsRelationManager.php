<?php

namespace Modules\Farms\Filament\Resources\CropCycleResource\RelationManagers;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;
use Modules\Farms\Models\CropScoutingRecord;

class ScoutingRecordsRelationManager extends RelationManager
{
    protected static string $relationship = 'scoutingRecords';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
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
            Textarea::make('description')->required()->rows(3)->columnSpanFull(),
            Textarea::make('recommended_action')->rows(2)->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('scouting_date')->date('d M Y')->sortable(),
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
            ->headerActions([CreateAction::make()])
            ->actions([
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
}