<?php

namespace Modules\Requisition\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\Requisition\Filament\Resources\RequisitionCustomFieldResource\Pages;
use Modules\Requisition\Models\Requisition;
use Modules\Requisition\Models\RequisitionCustomField;

class RequisitionCustomFieldResource extends Resource
{
    protected static ?string $model = RequisitionCustomField::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected static string|\UnitEnum|null $navigationGroup = 'Requisitions';

    protected static ?int $navigationSort = 11;

    protected static ?string $navigationLabel = 'Custom Fields';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Field Definition')->schema([
                Grid::make(2)->schema([
                    Select::make('request_type')
                        ->label('Applies To Request Type')
                        ->options(array_merge(['all' => 'All Types'], Requisition::TYPES))
                        ->required(),
                    TextInput::make('field_label')->label('Field Label')->required()->maxLength(255),
                ]),
                Grid::make(2)->schema([
                    TextInput::make('field_key')
                        ->label('Field Key (machine-readable)')
                        ->required()
                        ->maxLength(100)
                        ->helperText('Lowercase letters, numbers and underscores only. E.g. sow_description')
                        ->regex('/^[a-z0-9_]+$/'),
                    Select::make('field_type')
                        ->label('Field Type')
                        ->options([
                            'text'     => 'Text',
                            'textarea' => 'Text Area',
                            'number'   => 'Number',
                            'date'     => 'Date',
                            'select'   => 'Select (dropdown)',
                            'checkbox' => 'Checkbox',
                        ])
                        ->required()
                        ->live(),
                ]),
                Repeater::make('field_options')
                    ->label('Select Options')
                    ->schema([
                        TextInput::make('option')->required()->label('Option'),
                    ])
                    ->addActionLabel('Add Option')
                    ->reorderable()
                    ->columnSpanFull()
                    ->visible(fn (\Filament\Schemas\Components\Component $component) =>
                        ($component->getLivewire()->data['field_type'] ?? '') === 'select'
                    ),
                Grid::make(3)->schema([
                    Toggle::make('is_required')->label('Required')->default(false)->inline(false),
                    Toggle::make('is_active')->label('Active')->default(true)->inline(false),
                    TextInput::make('sort_order')->label('Sort Order')->numeric()->default(0),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('field_label')->label('Field Label')->searchable(),
                TextColumn::make('request_type')
                    ->label('Applies To')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state === 'all' ? 'All Types' : (Requisition::TYPES[$state] ?? $state)),
                TextColumn::make('field_type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                IconColumn::make('is_required')
                    ->label('Required')
                    ->boolean(),
                TextColumn::make('sort_order')->label('Order')->sortable(),
                ToggleColumn::make('is_active')->label('Active'),
            ])
            ->defaultSort('sort_order', 'asc')
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListRequisitionCustomFields::route('/'),
            'create' => Pages\CreateRequisitionCustomField::route('/create'),
            'edit'   => Pages\EditRequisitionCustomField::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query  = parent::getEloquentQuery();
        $tenant = filament()->getTenant();

        if ($tenant) {
            $query->where('company_id', $tenant->getKey());
        }

        return $query;
    }
}