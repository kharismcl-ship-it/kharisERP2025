<?php
namespace Modules\Finance\Filament\Resources\AssetCategoryResource\Pages;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Finance\Filament\Resources\AssetCategoryResource;
class ListAssetCategories extends ListRecords {
    protected static string $resource = AssetCategoryResource::class;
    protected function getHeaderActions(): array { return [CreateAction::make()]; }
}
