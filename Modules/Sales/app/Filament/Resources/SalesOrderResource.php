<?php

namespace Modules\Sales\Filament\Resources;

use Filament\Actions\EditAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Modules\Sales\Filament\Resources\SalesOrderResource\Pages;
use Modules\Sales\Models\SalesOrder;

class SalesOrderResource extends Resource
{
    protected static ?string $model = SalesOrder::class;

    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-shopping-bag';
    protected static string|\UnitEnum|null   $navigationGroup = 'Orders';
    protected static ?int                    $navigationSort  = 31;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Order Header')->columns(2)->schema([
                TextInput::make('reference')->disabled()->dehydrated(false)->placeholder('Auto-generated'),
                Select::make('status')
                    ->options(array_combine(SalesOrder::STATUSES, array_map('ucfirst', SalesOrder::STATUSES)))
                    ->default('pending'),
                Select::make('contact_id')
                    ->label('Contact')
                    ->relationship('contact', 'first_name')
                    ->searchable()->preload(),
                Select::make('organization_id')
                    ->label('Organization')
                    ->relationship('organization', 'name')
                    ->searchable()->preload(),
                Textarea::make('notes')->rows(2)->columnSpanFull(),
            ]),
            Section::make('Line Items')->schema([
                Repeater::make('lines')
                    ->relationship()
                    ->schema([
                        Select::make('catalog_item_id')
                            ->label('Item')
                            ->relationship('catalogItem', 'name')
                            ->searchable()->preload()->required()
                            ->columnSpan(4),
                        TextInput::make('quantity')->numeric()->default(1)->required()->columnSpan(1),
                        TextInput::make('unit_price')->numeric()->prefix('GHS')->required()->columnSpan(2),
                        TextInput::make('discount_pct')->numeric()->suffix('%')->default(0)->columnSpan(1),
                        TextInput::make('line_total')->numeric()->disabled()->dehydrated(false)->columnSpan(2),
                    ])
                    ->columns(10)
                    ->defaultItems(1),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference')->searchable()->sortable(),
                TextColumn::make('contact.first_name')->label('Contact'),
                TextColumn::make('organization.name')->label('Organization'),
                TextColumn::make('status')->badge()
                    ->color(fn (string $state) => match ($state) {
                        'fulfilled'  => 'success',
                        'cancelled'  => 'danger',
                        'confirmed'  => 'warning',
                        'processing' => 'info',
                        default      => 'gray',
                    }),
                TextColumn::make('total')->money('GHS')->sortable(),
                TextColumn::make('confirmed_at')->dateTime()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('fulfilled_at')->dateTime()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')->options(array_combine(SalesOrder::STATUSES, array_map('ucfirst', SalesOrder::STATUSES))),
            ])
            ->actions([EditAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSalesOrders::route('/'),
            'create' => Pages\CreateSalesOrder::route('/create'),
            'edit'   => Pages\EditSalesOrder::route('/{record}/edit'),
        ];
    }
}