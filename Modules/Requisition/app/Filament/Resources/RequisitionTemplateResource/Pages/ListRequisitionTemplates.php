<?php

namespace Modules\Requisition\Filament\Resources\RequisitionTemplateResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Modules\Requisition\Database\Seeders\RequisitionTemplateSeeder;
use Modules\Requisition\Filament\Resources\RequisitionTemplateResource;
use Modules\Requisition\Models\RequisitionTemplate;

class ListRequisitionTemplates extends ListRecords
{
    protected static string $resource = RequisitionTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('import_defaults')
                ->label('Import Default Templates')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Import Default Templates')
                ->modalDescription(fn () => $this->importModalDescription())
                ->modalSubmitActionLabel('Import')
                ->action(function (): void {
                    $tenant    = filament()->getTenant();
                    $companyId = $tenant?->getKey();

                    if (! $companyId) {
                        Notification::make()
                            ->warning()
                            ->title('No active company')
                            ->body('Switch to a company panel before importing templates.')
                            ->send();
                        return;
                    }

                    $created = RequisitionTemplateSeeder::importForCompany($companyId);

                    if ($created === 0) {
                        Notification::make()
                            ->info()
                            ->title('Already up to date')
                            ->body('All default templates are already present for this company.')
                            ->send();
                    } else {
                        Notification::make()
                            ->success()
                            ->title("{$created} template(s) imported")
                            ->body("Successfully imported {$created} default template(s) into your company.")
                            ->send();
                    }
                }),

            CreateAction::make(),
        ];
    }

    private function importModalDescription(): string
    {
        $tenant    = filament()->getTenant();
        $companyId = $tenant?->getKey();

        if (! $companyId) {
            return 'You must be in a company context to import templates.';
        }

        $total     = count(RequisitionTemplateSeeder::definitions());
        $existing  = RequisitionTemplate::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->whereIn('name', array_column(RequisitionTemplateSeeder::definitions(), 'name'))
            ->count();
        $toImport  = $total - $existing;

        if ($toImport === 0) {
            return "All {$total} default templates are already present for this company. Nothing will be imported.";
        }

        return "This will import {$toImport} of {$total} default template(s) for your company. "
            . ($existing > 0 ? "{$existing} template(s) already exist and will be skipped." : '');
    }
}