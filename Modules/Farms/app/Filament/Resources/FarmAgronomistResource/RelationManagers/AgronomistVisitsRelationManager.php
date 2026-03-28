<?php

namespace Modules\Farms\Filament\Resources\FarmAgronomistResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AgronomistVisitsRelationManager extends RelationManager
{
    protected static string $relationship = 'visits';

    protected static ?string $title = 'Visits';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('farm_id')
                ->label('Farm')
                ->relationship('farm', 'name')
                ->searchable()
                ->preload()
                ->required(),

            DatePicker::make('visit_date')->required()->default(today()),

            Select::make('visit_type')
                ->options([
                    'routine'           => 'Routine',
                    'problem_diagnosis' => 'Problem Diagnosis',
                    'compliance_audit'  => 'Compliance Audit',
                    'training'          => 'Training',
                    'other'             => 'Other',
                ])
                ->required(),

            Select::make('status')
                ->options([
                    'scheduled' => 'Scheduled',
                    'completed' => 'Completed',
                    'cancelled' => 'Cancelled',
                ])
                ->default('scheduled')
                ->required(),

            Textarea::make('observations')->rows(2)->columnSpanFull(),
            Textarea::make('recommendations')->rows(2)->columnSpanFull(),
            Toggle::make('follow_up_required')->default(false)->live()->inline(false),
            DatePicker::make('follow_up_date')->visible(fn ($get) => (bool) $get('follow_up_required')),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('farm.name')->label('Farm'),
                TextColumn::make('visit_type')->badge(),
                TextColumn::make('visit_date')->date()->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'scheduled' => 'info',
                        'cancelled' => 'danger',
                        default     => 'gray',
                    }),
                IconColumn::make('follow_up_required')->boolean()->label('Follow-up'),
            ])
            ->headerActions([CreateAction::make()])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('visit_date', 'desc');
    }
}