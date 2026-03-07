<?php

namespace Modules\Requisition\Filament\Resources;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Modules\Requisition\Filament\Resources\RequisitionTemplateResource\Pages;
use Modules\Requisition\Models\Requisition;
use Modules\Requisition\Models\RequisitionTemplate;

class RequisitionTemplateResource extends Resource
{
    protected static ?string $model = RequisitionTemplate::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-duplicate';

    protected static string|\UnitEnum|null $navigationGroup = 'Requisitions';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationLabel = 'Templates';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Template Details')->schema([
                Grid::make(2)->schema([
                    TextInput::make('name')->required()->maxLength(255),
                    Toggle::make('is_active')->label('Active')->default(true)->inline(false),
                ]),
                Textarea::make('description')->rows(2)->columnSpanFull(),
                Grid::make(3)->schema([
                    Select::make('request_type')
                        ->options(Requisition::TYPES)
                        ->required()
                        ->default('general'),
                    Select::make('urgency')
                        ->options(Requisition::URGENCIES)
                        ->required()
                        ->default('medium'),
                    Select::make('cost_centre_id')
                        ->label('Default Cost Centre')
                        ->relationship('costCentre', 'name')
                        ->searchable()
                        ->preload()
                        ->nullable(),
                ]),
                TextInput::make('default_title')
                    ->label('Default Title (optional)')
                    ->maxLength(255)
                    ->nullable()
                    ->columnSpanFull(),
            ]),

            Section::make('Default Items')->schema([
                Repeater::make('default_items')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('description')->required()->label('Item Description'),
                            TextInput::make('quantity')->numeric()->default(1)->required(),
                            TextInput::make('unit')->default('pcs')->required(),
                        ]),
                        Grid::make(2)->schema([
                            TextInput::make('unit_cost')->label('Unit Cost (GHS)')->numeric()->nullable(),
                            TextInput::make('notes')->label('Notes')->nullable(),
                        ]),
                    ])
                    ->addActionLabel('Add Item')
                    ->collapsible()
                    ->defaultItems(0)
                    ->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('request_type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => Requisition::TYPES[$state] ?? $state)
                    ->color(fn ($state) => match ($state) {
                        'fund'      => 'warning',
                        'material'  => 'info',
                        'equipment' => 'success',
                        'service'   => 'gray',
                        default     => 'primary',
                    }),
                TextColumn::make('urgency')->badge()
                    ->color(fn ($state) => match ($state) {
                        'urgent' => 'danger',
                        'high'   => 'warning',
                        'medium' => 'info',
                        default  => 'gray',
                    }),
                TextColumn::make('costCentre.name')->label('Cost Centre')->placeholder('—'),
                IconColumn::make('is_active')->label('Active')->boolean(),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name')
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListRequisitionTemplates::route('/'),
            'create' => Pages\CreateRequisitionTemplate::route('/create'),
            'edit'   => Pages\EditRequisitionTemplate::route('/{record}/edit'),
        ];
    }
}
