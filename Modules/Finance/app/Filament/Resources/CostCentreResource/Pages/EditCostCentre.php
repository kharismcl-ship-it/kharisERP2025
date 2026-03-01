<?php
namespace Modules\Finance\Filament\Resources\CostCentreResource\Pages;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Modules\Finance\Filament\Resources\CostCentreResource;
class EditCostCentre extends EditRecord {
    protected static string $resource = CostCentreResource::class;
    protected function getHeaderActions(): array { return [ViewAction::make(), DeleteAction::make()]; }
}
