<?php

namespace Modules\Hostels\Filament\Resources;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Hostels\Enums\AssignmentStatus;
use Modules\Hostels\Filament\Resources\RoomInventoryAssignmentResource\Pages;
use Modules\Hostels\Models\RoomInventoryAssignment;

class RoomInventoryAssignmentResource extends Resource
{
    protected static ?string $model = RoomInventoryAssignment::class;

    protected static ?string $slug = 'room-inventory-assignments';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;

    protected static string|\UnitEnum|null $navigationGroup = 'Inventory Management';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Assignment Details')
                    ->schema([
                        Select::make('room_id')
                            ->relationship('room', 'room_number')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Room'),

                        Select::make('inventory_item_id')
                            ->relationship('inventoryItem', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Inventory Item'),

                        TextInput::make('quantity')
                            ->label('Quantity')
                            ->numeric()
                            ->minValue(1)
                            ->default(1)
                            ->required(),

                        ToggleButtons::make('status')
                            ->label('Status')
                            ->options(AssignmentStatus::class)
                            ->default(AssignmentStatus::ACTIVE)
                            ->required()
                            ->inline(),

                        DateTimePicker::make('assigned_at')
                            ->label('Assigned At')
                            ->default(now())
                            ->required(),

                        DateTimePicker::make('removed_at')
                            ->label('Removed At')
                            ->nullable(),

                        Textarea::make('notes')
                            ->label('Notes')
                            ->nullable()
                            ->columnSpanFull(),

                        Textarea::make('condition_notes')
                            ->label('Condition Notes')
                            ->nullable()
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('room.name')
                    ->label('Room')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('inventoryItem.name')
                    ->label('Inventory Item')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('quantity')
                    ->label('Qty')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (AssignmentStatus $state) => $state->color())
                    ->formatStateUsing(fn (AssignmentStatus $state) => $state->label())
                    ->sortable(),

                TextColumn::make('assigned_at')
                    ->label('Assigned')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('removed_at')
                    ->label('Removed')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('status')
                    ->options(AssignmentStatus::class)
                    ->label('Status'),

                \Filament\Tables\Filters\SelectFilter::make('room_id')
                    ->label('Room')
                    ->relationship('room', 'name')
                    ->searchable(),

                \Filament\Tables\Filters\SelectFilter::make('inventory_item_id')
                    ->label('Inventory Item')
                    ->relationship('inventoryItem', 'name')
                    ->searchable(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
                Action::make('markDamaged')
                    ->label('Mark Damaged')
                    ->icon(Heroicon::OutlinedExclamationTriangle)
                    ->color('danger')
                    ->action(function (RoomInventoryAssignment $record) {
                        $record->markAsDamaged('Marked as damaged via admin panel');
                    })
                    ->visible(fn (RoomInventoryAssignment $record) => $record->isActive()),

                Action::make('markLost')
                    ->label('Mark Lost')
                    ->icon(Heroicon::OutlinedQuestionMarkCircle)
                    ->color('warning')
                    ->action(function (RoomInventoryAssignment $record) {
                        $record->markAsLost();
                    })
                    ->visible(fn (RoomInventoryAssignment $record) => $record->isActive()),

                Action::make('reactivate')
                    ->label('Reactivate')
                    ->icon(Heroicon::OutlinedArrowPath)
                    ->color('success')
                    ->action(function (RoomInventoryAssignment $record) {
                        $record->reactivate();
                    })
                    ->visible(fn (RoomInventoryAssignment $record) => ! $record->isActive()),
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
            // Relation managers for maintenance records can be added here
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoomInventoryAssignments::route('/'),
            'create' => Pages\CreateRoomInventoryAssignment::route('/create'),
            'edit' => Pages\EditRoomInventoryAssignment::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'primary';
    }
}
