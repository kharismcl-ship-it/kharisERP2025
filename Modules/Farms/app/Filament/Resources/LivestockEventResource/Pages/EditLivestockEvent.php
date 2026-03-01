<?php
namespace Modules\Farms\Filament\Resources\LivestockEventResource\Pages;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Farms\Filament\Resources\LivestockEventResource;
class EditLivestockEvent extends EditRecord {
    protected static string $resource = LivestockEventResource::class;
    protected function getHeaderActions(): array { return [ViewAction::make(), DeleteAction::make()]; }
}
