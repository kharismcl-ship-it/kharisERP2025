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
use Modules\Farms\Models\LivestockHealthRecord;

class HealthRecordsRelationManager extends RelationManager
{
    protected static string $relationship = 'healthRecords';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('event_type')
                ->options(array_combine(
                    LivestockHealthRecord::EVENT_TYPES,
                    array_map('ucwords', array_map(fn ($v) => str_replace('_', ' ', $v), LivestockHealthRecord::EVENT_TYPES))
                ))
                ->required(),
            DatePicker::make('event_date')->required(),
            TextInput::make('administered_by')->maxLength(255),
            Textarea::make('description')->required()->rows(2)->columnSpanFull(),
            TextInput::make('medicine_used')->maxLength(255),
            TextInput::make('dosage')->maxLength(255),
            TextInput::make('cost')->numeric()->prefix('GHS')->step(0.01),
            DatePicker::make('next_due_date')->label('Next Due Date'),
            Textarea::make('notes')->rows(2)->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('event_type')->badge()
                    ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state)))
                    ->color('primary'),
                TextColumn::make('event_date')->date('d M Y')->sortable(),
                TextColumn::make('description')->limit(30),
                TextColumn::make('medicine_used')->label('Medicine')->placeholder('—'),
                TextColumn::make('cost')->money('GHS'),
                TextColumn::make('next_due_date')->date('d M Y')->label('Next Due')
                    ->color(fn ($state) => $state && now()->gte($state) ? 'danger' : null),
            ])
            ->headerActions([CreateAction::make()])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('event_date', 'desc');
    }
}
