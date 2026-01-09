<?php

namespace Modules\HR\Filament\Resources\PerformanceCycleResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class PerformanceReviewsRelationManager extends RelationManager
{
    protected static string $relationship = 'performanceReviews';

    protected static ?string $label = 'Performance Reviews';

    protected static ?string $title = 'Performance Reviews';

    public function schema(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Select::make('employee_id')
                    ->relationship('employee', 'full_name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('reviewer_employee_id')
                    ->relationship('reviewer', 'full_name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\DatePicker::make('review_date')
                    ->required(),
                Forms\Components\Textarea::make('comments')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\Select::make('rating')
                    ->options([
                        1 => '1 - Poor',
                        2 => '2 - Below Average',
                        3 => '3 - Average',
                        4 => '4 - Good',
                        5 => '5 - Excellent',
                    ])
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.full_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reviewer.full_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('review_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rating')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('rating')
                    ->options([
                        1 => '1 - Poor',
                        2 => '2 - Below Average',
                        3 => '3 - Average',
                        4 => '4 - Good',
                        5 => '5 - Excellent',
                    ]),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
