<?php
namespace Modules\Finance\Filament\Resources\FixedAssetResource\Pages;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Finance\Filament\Resources\FixedAssetResource;
class EditFixedAsset extends EditRecord {
    protected static string $resource = FixedAssetResource::class;
    protected function getHeaderActions(): array { return [ViewAction::make(), DeleteAction::make()]; }
}
