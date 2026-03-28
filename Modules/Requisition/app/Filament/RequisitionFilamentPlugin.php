<?php

namespace Modules\Requisition\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\Requisition\Filament\Pages\BulkRequisitionImportPage;
use Modules\Requisition\Filament\Pages\RequisitionDashboard;
use Modules\Requisition\Filament\Resources\RequisitionApprovalDelegationResource;
use Modules\Requisition\Filament\Resources\RequisitionCustomFieldResource;
use Modules\Requisition\Filament\Resources\RequisitionGrnResource;
use Modules\Requisition\Filament\Resources\RequisitionReminderRuleResource;
use Modules\Requisition\Filament\Resources\RequisitionResource;
use Modules\Requisition\Filament\Resources\RequisitionRfqResource;
use Modules\Requisition\Filament\Resources\RequisitionScheduleResource;
use Modules\Requisition\Filament\Resources\RequisitionTemplateResource;
use Modules\Requisition\Filament\Resources\RequisitionWorkflowRuleResource;
use Modules\Requisition\Filament\Widgets\RequisitionChartWidget;
use Modules\Requisition\Filament\Widgets\RequisitionCycleTimeWidget;
use Modules\Requisition\Filament\Widgets\RequisitionSpendWidget;
use Modules\Requisition\Filament\Widgets\RequisitionStatsWidget;

class RequisitionFilamentPlugin implements Plugin
{
    public function getId(): string
    {
        return 'requisition';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                RequisitionResource::class,
                RequisitionTemplateResource::class,
                RequisitionWorkflowRuleResource::class,
                RequisitionRfqResource::class,
                RequisitionGrnResource::class,
                RequisitionScheduleResource::class,
                RequisitionReminderRuleResource::class,
                RequisitionCustomFieldResource::class,
                RequisitionApprovalDelegationResource::class,
            ])
            ->pages([
                RequisitionDashboard::class,
                BulkRequisitionImportPage::class,
            ])
            ->widgets([
                RequisitionStatsWidget::class,
                RequisitionChartWidget::class,
                RequisitionSpendWidget::class,
                RequisitionCycleTimeWidget::class,
            ]);
    }

    public function boot(Panel $panel): void {}

    public static function make(): static
    {
        return app(static::class);
    }
}