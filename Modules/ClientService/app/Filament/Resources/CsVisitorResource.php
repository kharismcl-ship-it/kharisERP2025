<?php

namespace Modules\ClientService\Filament\Resources;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Saade\FilamentAutograph\Forms\Components\SignaturePad;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Modules\ClientService\Filament\Resources\CsVisitorResource\Pages;
use Modules\ClientService\Models\CsVisitor;

class CsVisitorResource extends Resource
{
    protected static ?string $model = CsVisitor::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static string|\UnitEnum|null $navigationGroup = 'Client Services';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Visitor Log';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Visitor Information')->schema([
                Select::make('company_id')
                    ->label('Company')
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->columnSpanFull(),
                Grid::make(3)->schema([
                    TextInput::make('full_name')->required()->maxLength(255),
                    TextInput::make('phone')->tel()->nullable(),
                    TextInput::make('email')->email()->nullable(),
                ]),
                Grid::make(3)->schema([
                    Select::make('id_type')
                        ->options(CsVisitor::ID_TYPES)
                        ->nullable(),
                    TextInput::make('id_number')->nullable(),
                    TextInput::make('organization')->nullable(),
                ]),
                FileUpload::make('photo_path')
                    ->label('Photo')
                    ->directory('visitor-photos')
                    ->image()
                    ->nullable()
                    ->columnSpanFull(),
                SignaturePad::make('check_in_signature')
                    ->label('Visitor Signature')
                    ->clearAction(fn (Action $action) => $action->button())
                    ->downloadAction(fn (Action $action) => $action->color('primary'))
                    ->undoAction(fn (Action $action) => $action->icon('heroicon-o-pencil'))
                    ->doneAction(fn (Action $action) => $action->iconButton()->icon('heroicon-o-thumbs-up'))
            ]),

            Section::make('Visit Details')->schema([
                Grid::make(2)->schema([
                    Select::make('host_employee_id')
                        ->label('Host Employee')
                        ->relationship('hostEmployee', 'full_name')
                        ->searchable()
                        ->preload()
                        ->nullable(),
                    Select::make('department_id')
                        ->label('Department')
                        ->relationship('department', 'name')
                        ->searchable()
                        ->preload()
                        ->nullable(),
                ]),
                Grid::make(2)->schema([
                    TextInput::make('badge_number')->nullable(),
                    Textarea::make('items_brought')->rows(2)->nullable(),
                ]),
                Textarea::make('purpose_of_visit')->required()->rows(3)->columnSpanFull(),
                Textarea::make('notes')->rows(2)->columnSpanFull(),
            ]),

            Section::make('Check In / Check Out Times')->schema([
                Grid::make(2)->schema([
                    DateTimePicker::make('check_in_at')->required()->default(now()),
                    DateTimePicker::make('check_out_at')->nullable(),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')->searchable()->sortable(),
                TextColumn::make('organization')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('phone'),
                TextColumn::make('hostEmployee.full_name')->label('Host'),
                TextColumn::make('check_in_at')->dateTime()->sortable(),
                TextColumn::make('check_out_at')
                    ->label('Check Out')
                    ->dateTime()
                    ->formatStateUsing(fn ($state) => $state ? $state : 'Still In'),
                TextColumn::make('duration')
                    ->label('Duration')
                    ->state(fn (CsVisitor $record) => $record->duration ?? '—'),
                TextColumn::make('is_checked_out')
                    ->label('Status')
                    ->badge()
                    ->state(fn (CsVisitor $record) => $record->is_checked_out ? 'Out' : 'In')
                    ->color(fn ($state) => $state === 'Out' ? 'success' : 'warning'),
            ])
            ->defaultSort('check_in_at', 'desc')
            ->filters([
                SelectFilter::make('host_employee_id')
                    ->label('Host Employee')
                    ->relationship('hostEmployee', 'full_name'),
                SelectFilter::make('department_id')
                    ->label('Department')
                    ->relationship('department', 'name'),
                Filter::make('still_in')
                    ->label('Still In (Not Checked Out)')
                    ->query(fn ($query) => $query->whereNull('check_out_at')),
                Filter::make('today')
                    ->label('Today\'s Visitors')
                    ->query(fn ($query) => $query->whereDate('check_in_at', today())),
            ])
            ->actions([
                \Filament\Actions\Action::make('check_out')
                    ->label('Check Out')
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->color('warning')
                    ->visible(fn (CsVisitor $record) => ! $record->is_checked_out)
                    ->action(fn (CsVisitor $record) => $record->update(['check_out_at' => now()])),
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCsVisitors::route('/'),
            'create' => Pages\CreateCsVisitor::route('/create'),
            'view'   => Pages\ViewCsVisitor::route('/{record}'),
            'edit'   => Pages\EditCsVisitor::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['full_name', 'phone', 'email'];
    }
}
