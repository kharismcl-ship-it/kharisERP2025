<?php

namespace Modules\Farms\Filament\Resources\FarmSeasonResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;
use Modules\Farms\Models\FarmMilestone;

class FarmMilestonesRelationManager extends RelationManager
{
    protected static string $relationship = 'milestones';

    protected static ?string $title = 'Milestones';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->required()->maxLength(255)->columnSpanFull(),

            Select::make('milestone_type')
                ->label('Type')
                ->options([
                    'land_prep'   => 'Land Preparation',
                    'planting'    => 'Planting',
                    'growing'     => 'Growing',
                    'scouting'    => 'Scouting',
                    'harvesting'  => 'Harvesting',
                    'selling'     => 'Selling',
                    'reporting'   => 'Reporting',
                    'other'       => 'Other',
                ])
                ->required(),

            Select::make('status')
                ->options([
                    'pending'     => 'Pending',
                    'in_progress' => 'In Progress',
                    'completed'   => 'Completed',
                    'missed'      => 'Missed',
                ])
                ->default('pending')
                ->required(),

            DatePicker::make('target_date')->required(),
            DatePicker::make('actual_date')->nullable(),

            Textarea::make('description')->rows(2)->columnSpanFull(),
            Textarea::make('progress_notes')->label('Progress Notes')->rows(2)->columnSpanFull(),

            FileUpload::make('attachments')
                ->multiple()
                ->image()
                ->maxFiles(5)
                ->directory('farm-milestones')
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable(),
                TextColumn::make('milestone_type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ucwords(str_replace('_', ' ', $state)))
                    ->color('primary'),
                TextColumn::make('target_date')->date()->sortable(),
                TextColumn::make('actual_date')->date()->placeholder('—'),
                TextColumn::make('status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending'     => 'gray',
                        'in_progress' => 'warning',
                        'completed'   => 'success',
                        'missed'      => 'danger',
                        default       => 'gray',
                    }),
            ])
            ->headerActions([CreateAction::make()])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('target_date');
    }
}
