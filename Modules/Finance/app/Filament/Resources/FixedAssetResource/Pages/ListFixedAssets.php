<?php
namespace Modules\Finance\Filament\Resources\FixedAssetResource\Pages;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Finance\Filament\Resources\FixedAssetResource;
class ListFixedAssets extends ListRecords {
    protected static string $resource = FixedAssetResource::class;
    protected function getHeaderActions(): array { return [CreateAction::make()]; }
}
