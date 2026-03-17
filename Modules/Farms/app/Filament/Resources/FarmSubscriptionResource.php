<?php

namespace Modules\Farms\Filament\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Modules\Farms\Filament\Clusters\FarmMarketplaceCluster;
use Modules\Farms\Filament\Resources\FarmSubscriptionResource\Pages;
use Modules\Farms\Models\FarmSubscription;

class FarmSubscriptionResource extends Resource
{
    protected static ?string $model = FarmSubscription::class;

    protected static ?string $cluster = FarmMarketplaceCluster::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path';

    protected static string|\UnitEnum|null $navigationGroup = 'Farms';

    protected static ?int $navigationSort = 6;

    protected static ?string $navigationLabel = 'Subscriptions';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Subscription Details')
                ->columns(3)
                ->schema([
                    TextEntry::make('shopCustomer.name')->label('Customer'),
                    TextEntry::make('shopCustomer.email')->label('Email')->placeholder('—'),
                    TextEntry::make('shopCustomer.phone')->label('Phone')->placeholder('—'),
                    TextEntry::make('frequency')->badge()->formatStateUsing(fn ($s) => FarmSubscription::FREQUENCIES[$s] ?? ucfirst($s)),
                    TextEntry::make('status')->badge()->color(fn ($s) => match ($s) {
                        'active' => 'success', 'paused' => 'warning', 'cancelled' => 'danger', default => 'gray',
                    }),
                    TextEntry::make('subtotal')->money('GHS'),
                    TextEntry::make('next_order_date')->date()->label('Next Order'),
                    TextEntry::make('last_order_date')->date()->label('Last Order')->placeholder('None yet'),
                    TextEntry::make('delivery_type')->formatStateUsing(fn ($s) => ucfirst($s)),
                    TextEntry::make('delivery_address')->columnSpanFull()->placeholder('—'),
                    TextEntry::make('notes')->columnSpanFull()->placeholder('—'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('shopCustomer.name')->label('Customer')->searchable(),
                TextColumn::make('shopCustomer.phone')->label('Phone')->placeholder('—'),
                TextColumn::make('frequency')->badge()
                    ->formatStateUsing(fn ($s) => FarmSubscription::FREQUENCIES[$s] ?? ucfirst($s)),
                BadgeColumn::make('status')
                    ->colors(['success' => 'active', 'warning' => 'paused', 'danger' => 'cancelled']),
                TextColumn::make('subtotal')->money('GHS'),
                TextColumn::make('next_order_date')->date()->label('Next Order')->sortable(),
                TextColumn::make('delivery_type')->badge()->formatStateUsing(fn ($s) => ucfirst($s)),
            ])
            ->defaultSort('next_order_date')
            ->filters([
                SelectFilter::make('status')->options([
                    'active'    => 'Active',
                    'paused'    => 'Paused',
                    'cancelled' => 'Cancelled',
                ]),
                SelectFilter::make('frequency')->options(FarmSubscription::FREQUENCIES),
            ])
            ->actions([ViewAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFarmSubscriptions::route('/'),
            'view'  => Pages\ViewFarmSubscription::route('/{record}'),
        ];
    }
}
