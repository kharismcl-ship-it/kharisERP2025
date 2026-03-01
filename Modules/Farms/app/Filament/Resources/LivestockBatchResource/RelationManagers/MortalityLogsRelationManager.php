<?php

namespace Modules\Farms\Filament\Resources\LivestockBatchResource\RelationManagers;

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
use Modules\Farms\Models\LivestockMortalityLog;

class MortalityLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'mortalityLogs';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            DatePicker::make('event_date')->required(),
            TextInput::make('count')->required()->numeric()->minValue(1)->default(1),
            Select::make('cause')
                ->options(array_combine(
                    LivestockMortalityLog::CAUSES,
                    array_map('ucfirst', LivestockMortalityLog::CAUSES)
                ))
                ->required(),
            Textarea::make('description')->rows(2)->columnSpanFull(),
            Textarea::make('notes')->rows(2)->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('event_date')->date('d M Y')->sortable(),
                TextColumn::make('count')->label('Animals'),
                TextColumn::make('cause')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'disease' => 'danger',
                        'injury'  => 'warning',
                        default   => 'gray',
                    }),
                TextColumn::make('description')->limit(40)->placeholder('—'),
            ])
            ->headerActions([CreateAction::make()])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('event_date', 'desc');
    }
}
