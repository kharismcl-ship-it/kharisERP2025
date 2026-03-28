<?php

/**
 * VendorRfqResource — read-only RFQ view for the Vendor Panel.
 *
 * NOTE: To enable this resource in the vendor panel, add the following to
 * VendorPanelProvider in app/Providers/Filament/VendorPanelProvider.php:
 *
 *   ->plugins([
 *       ...
 *       \Modules\Requisition\Filament\RequisitionVendorPlugin::make(),
 *   ])
 */

namespace Modules\Requisition\Filament\Vendor;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\Requisition\Models\RequisitionRfq;

class VendorRfqResource extends Resource
{
    protected static ?string $model = RequisitionRfq::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-scale';

    protected static string|\UnitEnum|null $navigationGroup = 'Procurement';

    protected static ?string $navigationLabel = 'RFQ Invitations';

    protected static ?string $slug = 'vendor-rfqs';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        $vendorId = static::resolveVendorId();

        return $table
            ->columns([
                TextColumn::make('rfq_number')->label('RFQ #')->badge()->searchable(),
                TextColumn::make('title')->limit(40),
                TextColumn::make('deadline')->label('Bid Deadline')->date()->placeholder('—'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'draft'      => 'gray',
                        'sent'       => 'info',
                        'evaluating' => 'warning',
                        'awarded'    => 'success',
                        'cancelled'  => 'danger',
                        default      => 'gray',
                    }),
                TextColumn::make('bids')
                    ->label('My Bid Amount')
                    ->formatStateUsing(function ($state, $record) use ($vendorId) {
                        if (! $vendorId) return '—';
                        $bid = $record->bids->firstWhere('vendor_id', $vendorId);
                        return $bid ? 'GHS ' . number_format((float) $bid->quoted_amount, 2) : '—';
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        $vendorId = static::resolveVendorId();

        return parent::getEloquentQuery()
            ->with('bids')
            ->where(function (Builder $q) use ($vendorId) {
                $q->where('awarded_vendor_id', $vendorId)
                    ->orWhereHas('bids', fn (Builder $bq) => $bq->where('vendor_id', $vendorId));
            });
    }

    protected static function resolveVendorId(): ?int
    {
        // In the vendor panel, the authenticated user IS a VendorContact (vendor guard)
        $user = auth()->user();
        if (! $user) return null;

        // VendorContact has vendor_id directly
        if (property_exists($user, 'vendor_id') || isset($user->vendor_id)) {
            return (int) $user->vendor_id;
        }

        return null;
    }

    public static function getPages(): array
    {
        return [];
    }
}