<?php

namespace Modules\Finance\Filament\Resources\FixedAssetResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use Modules\Finance\Filament\Resources\FixedAssetResource;
use Modules\Finance\Models\FixedAsset;

class ListFixedAssets extends ListRecords
{
    protected static string $resource = FixedAssetResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }

    public function getTabs(): array
    {
        $activeCount      = FixedAsset::withoutGlobalScopes()->where('status', 'active')->count();
        $disposedCount    = FixedAsset::withoutGlobalScopes()->where('status', 'disposed')->count();
        $writtenOffCount  = FixedAsset::withoutGlobalScopes()->where('status', 'written_off')->count();

        return [
            'all' => Tab::make('All Assets'),

            'active' => Tab::make('Active')
                ->badge($activeCount ?: null)
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('status', 'active')),

            'disposed' => Tab::make('Disposed')
                ->badge($disposedCount ?: null)
                ->badgeColor('gray')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('status', 'disposed')),

            'written_off' => Tab::make('Written Off')
                ->badge($writtenOffCount ?: null)
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('status', 'written_off')),
        ];
    }
}
