<?php

namespace Modules\HR\Filament\Resources\EmployeeResource\RelationManagers;

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

class ReviewsGivenRelationManager extends RelationManager
{
    protected static string $relationship = 'reviewsGiven';

    protected static ?string $label = 'Reviews Given';

    protected static ?string $title = 'Performance Reviews Given';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('employee_id')
                    ->relationship('employee', 'full_name')
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
