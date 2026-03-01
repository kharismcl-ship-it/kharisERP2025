<?php
namespace Modules\Finance\Filament\Resources\AssetCategoryResource\Pages;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Finance\Filament\Resources\AssetCategoryResource;
class EditAssetCategory extends EditRecord {
    protected static string $resource = AssetCategoryResource::class;
    protected function getHeaderActions(): array { return [ViewAction::make(), DeleteAction::make()]; }
}
