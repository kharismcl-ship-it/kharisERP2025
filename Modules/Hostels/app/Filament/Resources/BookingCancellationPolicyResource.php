<?php

namespace Modules\Hostels\Filament\Resources;

use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\Hostels\Filament\Resources\BookingCancellationPolicyResource\Pages;
use Modules\Hostels\Models\BookingCancellationPolicy;

class BookingCancellationPolicyResource extends Resource
{
    protected static ?string $model = BookingCancellationPolicy::class;

    protected static ?string $slug = 'booking-cancellation-policies';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendar;

    protected static string|\UnitEnum|null $navigationGroup = 'Hostels';

    protected static ?int $navigationSort = 50;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Policy Details')
                    ->components([
                        Select::make('hostel_id')
                            ->label('Hostel')
                            ->relationship('hostel', 'name')
                            ->nullable()
                            ->searchable()
                            ->preload()
                            ->helperText('Leave empty for default system-wide policy'),
                        TextInput::make('name')
                            ->label('Policy Name')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('description')
                            ->label('Description')
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ]),

                Section::make('Cancellation Rules')
                    ->components([
                        TextInput::make('cancellation_window_hours')
                            ->label('Cancellation Window (hours)')
                            ->numeric()
                            ->required()
                            ->default(24)
                            ->minValue(0)
                            ->helperText('Hours before check-in when cancellation is allowed'),
                        TextInput::make('refund_percentage')
                            ->label('Refund Percentage')
                            ->numeric()
                            ->required()
                            ->default(100.00)
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.01)
                            ->suffix('%')
                            ->helperText('Percentage of paid amount to refund upon cancellation'),
                    ]),

                Section::make('Status')
                    ->components([
                        Toggle::make('is_default')
                            ->label('Default Policy')
                            ->helperText('Mark as default system-wide policy')
                            ->default(false),
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('hostel.name')
                    ->label('Hostel')
                    ->sortable()
                    ->searchable()
                    ->placeholder('System Default'),
                TextColumn::make('name')
                    ->label('Policy Name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('cancellation_window_hours')
                    ->label('Window')
                    ->formatStateUsing(fn ($state) => "{$state} hours")
                    ->sortable(),
                TextColumn::make('refund_percentage')
                    ->label('Refund')
                    ->formatStateUsing(fn ($state) => "{$state}%")
                    ->sortable(),
                IconColumn::make('is_default')
                    ->label('Default')
                    ->boolean(),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('hostel_id')
                    ->label('Hostel')
                    ->relationship('hostel', 'name'),
                TernaryFilter::make('is_default')
                    ->label('Default Policy'),
                TernaryFilter::make('is_active')
                    ->label('Active Status'),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookingCancellationPolicies::route('/'),
            'create' => Pages\CreateBookingCancellationPolicy::route('/create'),
            'edit' => Pages\EditBookingCancellationPolicy::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('hostel');
    }
}
