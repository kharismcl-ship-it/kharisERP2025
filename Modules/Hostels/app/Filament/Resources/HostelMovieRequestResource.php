<?php

namespace Modules\Hostels\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\Hostels\Filament\Resources\HostelMovieRequestResource\Pages;
use Modules\Hostels\Models\HostelMovieRequest;

class HostelMovieRequestResource extends Resource
{
    protected static ?string $model = HostelMovieRequest::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedPlusCircle;

    protected static string|\UnitEnum|null $navigationGroup = 'Hostels';

    protected static ?int $navigationSort = 22;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('hostel_occupant_id')
                    ->relationship('occupant', 'full_name')
                    ->required(),
                Forms\Components\Select::make('hostel_id')
                    ->relationship('hostel', 'name')
                    ->required(),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\Select::make('urgency')
                    ->options([
                        'low'    => 'Low',
                        'normal' => 'Normal',
                        'urgent' => 'Urgent',
                    ])
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending'   => 'Pending',
                        'fulfilled' => 'Fulfilled',
                        'rejected'  => 'Rejected',
                    ])
                    ->required(),
                Forms\Components\Select::make('fulfilled_movie_id')
                    ->relationship('fulfilledMovie', 'title')
                    ->nullable()
                    ->label('Fulfilled Movie'),
                Forms\Components\Toggle::make('is_private')
                    ->label('Private (only requesting occupant can watch)'),
                Forms\Components\Textarea::make('admin_notes')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('hostel.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('occupant.full_name')
                    ->label('Occupant')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('urgency')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'low'    => 'gray',
                        'normal' => 'info',
                        'urgent' => 'danger',
                        default  => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending'   => 'warning',
                        'fulfilled' => 'success',
                        'rejected'  => 'gray',
                        default     => 'gray',
                    }),
                Tables\Columns\IconColumn::make('is_private')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending'   => 'Pending',
                        'fulfilled' => 'Fulfilled',
                        'rejected'  => 'Rejected',
                    ]),
                Tables\Filters\SelectFilter::make('urgency')
                    ->options([
                        'low'    => 'Low',
                        'normal' => 'Normal',
                        'urgent' => 'Urgent',
                    ]),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListHostelMovieRequests::route('/'),
            'create' => Pages\CreateHostelMovieRequest::route('/create'),
            'edit'   => Pages\EditHostelMovieRequest::route('/{record}/edit'),
        ];
    }
}
