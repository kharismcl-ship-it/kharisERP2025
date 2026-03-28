<?php

namespace Modules\ProcurementInventory\Filament\Resources;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\ProcurementInventory\Filament\Resources\BomResource\Pages;
use Modules\ProcurementInventory\Filament\Resources\BomResource\RelationManagers;
use Modules\ProcurementInventory\Models\Bom;

class BomResource extends Resource
{
    protected static ?string $model = Bom::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Inventory';

    protected static ?int $navigationSort = 35;

    protected static ?string $label = 'Bill of Materials';

    protected static ?string $pluralLabel = 'Bills of Materials';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\Section::make('BOM Details')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('company_id')
                        ->relationship('company', 'name')
                        ->searchable(),

                    Forms\Components\Select::make('item_id')
                        ->label('Finished Product / Assembly')
                        ->relationship('item', 'name')
                        ->searchable()
                        ->required(),

                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('version')
                        ->default('1.0')
                        ->required(),

                    Forms\Components\TextInput::make('quantity_produced')
                        ->label('Quantity Produced per BOM')
                        ->numeric()
                        ->default(1)
                        ->required(),

                    Forms\Components\TextInput::make('unit_of_measure')
                        ->label('UOM'),

                    Forms\Components\Toggle::make('is_active')
                        ->label('Active')
                        ->default(true),

                    Forms\Components\Textarea::make('notes')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('item.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('version')
                    ->badge(),

                Tables\Columns\TextColumn::make('quantity_produced')
                    ->label('Qty Produced')
                    ->numeric(4),

                Tables\Columns\TextColumn::make('lines_count')
                    ->counts('lines')
                    ->label('Components'),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
            ])
            ->actions([
                Action::make('generate_requisition')
                    ->label('Generate Requisition')
                    ->icon('heroicon-o-document-plus')
                    ->color('info')
                    ->form([
                        Forms\Components\TextInput::make('quantity')
                            ->label('Production Quantity')
                            ->numeric()
                            ->default(1)
                            ->required(),
                    ])
                    ->action(function (Bom $record, array $data) {
                        if (! class_exists(\Modules\Requisition\Models\Requisition::class)) {
                            Notification::make()->title('Requisition module not available')->warning()->send();
                            return;
                        }

                        $requirements = $record->generateProcurementRequirements((float) $data['quantity']);

                        $req = \Modules\Requisition\Models\Requisition::create([
                            'company_id'   => $record->company_id,
                            'request_type' => 'material',
                            'title'        => "BOM: {$record->name} × {$data['quantity']}",
                            'description'  => "Auto-generated from BOM {$record->name} v{$record->version}",
                            'urgency'      => 'medium',
                            'status'       => 'draft',
                        ]);

                        foreach ($requirements as $req_item) {
                            if (! $req_item['item_id']) continue;
                            \Modules\Requisition\Models\RequisitionItem::create([
                                'requisition_id' => $req->id,
                                'item_id'        => $req_item['item_id'],
                                'description'    => $req_item['item_name'],
                                'quantity'       => $req_item['quantity'],
                                'unit_of_measure'=> $req_item['unit_of_measure'],
                            ]);
                        }

                        Notification::make()
                            ->title("Draft requisition created with " . count($requirements) . " items")
                            ->success()
                            ->send();
                    }),

                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\BomLinesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListBoms::route('/'),
            'create' => Pages\CreateBom::route('/create'),
            'view'   => Pages\ViewBom::route('/{record}'),
            'edit'   => Pages\EditBom::route('/{record}/edit'),
        ];
    }
}