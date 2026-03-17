<?php

namespace Modules\Farms\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Modules\Farms\Filament\Clusters\FarmMarketplaceCluster;
use Modules\Farms\Filament\Resources\FarmReturnRequestResource\Pages;
use Modules\Farms\Models\FarmReturnRequest;

class FarmReturnRequestResource extends Resource
{
    protected static ?string $model = FarmReturnRequest::class;

    protected static ?string $cluster = FarmMarketplaceCluster::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-uturn-left';

    protected static string|\UnitEnum|null $navigationGroup = 'Farms';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Refund Requests';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Update Request')
                ->columns(2)
                ->schema([
                    Select::make('status')
                        ->options([
                            'pending'  => 'Pending',
                            'approved' => 'Approved',
                            'rejected' => 'Rejected',
                        ])
                        ->required(),
                    Textarea::make('admin_notes')
                        ->label('Admin Notes')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Return Request')
                ->columns(3)
                ->schema([
                    TextEntry::make('order.ref')
                        ->label('Order')
                        ->badge()
                        ->color('success'),
                    TextEntry::make('reason')
                        ->formatStateUsing(fn ($state) => FarmReturnRequest::REASONS[$state] ?? $state),
                    TextEntry::make('status')
                        ->badge()
                        ->color(fn ($state) => match ($state) {
                            'pending'  => 'warning',
                            'approved' => 'success',
                            'rejected' => 'danger',
                            default    => 'gray',
                        }),
                    TextEntry::make('description')
                        ->label('Customer Description')
                        ->columnSpanFull()
                        ->placeholder('—'),
                    TextEntry::make('created_at')
                        ->label('Submitted')
                        ->dateTime(),
                ]),

            Section::make('Order Info')
                ->columns(3)
                ->schema([
                    TextEntry::make('order.customer_name')->label('Customer'),
                    TextEntry::make('order.customer_phone')->label('Phone'),
                    TextEntry::make('order.total')->money('GHS')->label('Order Total'),
                    TextEntry::make('order.payment_status')
                        ->label('Payment')
                        ->badge()
                        ->color(fn ($state) => $state === 'paid' ? 'success' : 'warning'),
                ]),

            Section::make('Admin Response')
                ->collapsible()
                ->schema([
                    TextEntry::make('admin_notes')->label('Notes')->placeholder('—')->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order.ref')
                    ->label('Order Ref')
                    ->searchable()
                    ->badge()
                    ->color('success'),

                TextColumn::make('order.customer_name')
                    ->label('Customer')
                    ->searchable(),

                TextColumn::make('reason')
                    ->formatStateUsing(fn ($state) => FarmReturnRequest::REASONS[$state] ?? $state),

                TextColumn::make('order.total')
                    ->label('Amount')
                    ->money('GHS'),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'pending'  => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default    => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'pending'  => 'Pending',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                ]),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make()->label('Process'),
            ])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFarmReturnRequests::route('/'),
            'view'  => Pages\ViewFarmReturnRequest::route('/{record}'),
            'edit'  => Pages\EditFarmReturnRequest::route('/{record}/edit'),
        ];
    }
}
