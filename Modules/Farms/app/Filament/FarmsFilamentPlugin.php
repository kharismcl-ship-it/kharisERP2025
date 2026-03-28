<?php

namespace Modules\Farms\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\Farms\Filament\Clusters\CropsCluster;
use Modules\Farms\Filament\Clusters\FarmFinanceCluster;
use Modules\Farms\Filament\Clusters\FarmMarketplaceCluster;
use Modules\Farms\Filament\Clusters\FarmOperationsCluster;
use Modules\Farms\Filament\Clusters\LivestockCluster;
use Modules\Farms\Filament\Pages\CropYieldReport;
use Modules\Farms\Filament\Pages\FarmDashboard;
use Modules\Farms\Filament\Pages\FarmFinancialReport;
use Modules\Farms\Filament\Pages\FarmShopSettingsPage;
use Modules\Farms\Filament\Pages\LivestockReport;
use Modules\Farms\Filament\Resources\CropActivityResource;
use Modules\Farms\Filament\Resources\CropCycleResource;
use Modules\Farms\Filament\Resources\CropScoutingResource;
use Modules\Farms\Filament\Resources\CropVarietyResource;
use Modules\Farms\Filament\Resources\FarmBudgetResource;
use Modules\Farms\Filament\Resources\FarmDailyReportResource;
use Modules\Farms\Filament\Resources\FarmDocumentResource;
use Modules\Farms\Filament\Resources\FarmEquipmentResource;
use Modules\Farms\Filament\Resources\FarmExpenseResource;
use Modules\Farms\Filament\Resources\FarmBundleResource;
use Modules\Farms\Filament\Resources\FarmCouponResource;
use Modules\Farms\Filament\Resources\FarmOrderResource;
use Modules\Farms\Filament\Resources\FarmReturnRequestResource;
use Modules\Farms\Filament\Resources\FarmSubscriptionResource;
use Modules\Farms\Filament\Resources\FarmShopBannerResource;
use Modules\Farms\Filament\Resources\FarmShopNavItemResource;
use Modules\Farms\Filament\Resources\FarmB2bAccountResource;
use Modules\Farms\Filament\Resources\FarmShopBlogPostResource;
use Modules\Farms\Filament\Resources\FarmShopPageResource;
use Modules\Farms\Filament\Resources\FarmProduceInventoryResource;
use Modules\Farms\Filament\Resources\FarmRequestResource;
use Modules\Farms\Filament\Resources\FarmResource;
use Modules\Farms\Filament\Resources\FarmSaleResource;
use Modules\Farms\Filament\Resources\FarmSeasonResource;
use Modules\Farms\Filament\Resources\FarmTaskResource;
use Modules\Farms\Filament\Resources\FarmWeatherLogResource;
use Modules\Farms\Filament\Resources\FarmWorkerAttendanceResource;
use Modules\Farms\Filament\Resources\FarmWorkerResource;
use Modules\Farms\Filament\Resources\LivestockBatchResource;
use Modules\Farms\Filament\Resources\LivestockEventResource;
use Modules\Farms\Filament\Resources\LivestockHealthRecordResource;
use Modules\Farms\Filament\Resources\HarvestRecordResource;
use Modules\Farms\Filament\Resources\SoilTestRecordResource;
// Phase 3 — new resources
use Modules\Farms\Filament\Resources\FarmEquipmentLogResource;
use Modules\Farms\Filament\Resources\FarmInputChemicalResource;
use Modules\Farms\Filament\Resources\LivestockBreedingEventResource;
use Modules\Farms\Filament\Resources\FarmProduceLotResource;
use Modules\Farms\Filament\Resources\FarmNdviResource;
use Modules\Farms\Filament\Resources\FarmWeatherAlertResource;
use Modules\Farms\Filament\Resources\FarmStorageLocationResource;
use Modules\Farms\Filament\Resources\FarmPostHarvestResource;
// Phase 4 — gap features
use Modules\Farms\Filament\Resources\FarmTrialResource;
use Modules\Farms\Filament\Resources\FarmPastureResource;
use Modules\Farms\Filament\Resources\FarmInputCreditResource;
use Modules\Farms\Filament\Resources\FarmInputVoucherResource;
use Modules\Farms\Filament\Resources\FarmCarbonResource;
use Modules\Farms\Filament\Resources\FarmSmsCommandResource;
// Phase 3 — new pages
use Modules\Farms\Filament\Pages\SprayDiaryPage;
use Modules\Farms\Filament\Pages\LotTraceabilityPage;
use Modules\Farms\Filament\Pages\AdvancedFarmDashboardPage;
use Modules\Farms\Filament\Pages\FarmTaskKanbanPage;

class FarmsFilamentPlugin implements Plugin
{
    public function getId(): string
    {
        return 'farms';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            // Prominent sidebar (no cluster)
            FarmResource::class,
            FarmDailyReportResource::class,
            FarmDocumentResource::class,
            FarmRequestResource::class,
            FarmSeasonResource::class,
            // Crops cluster
            CropCycleResource::class,
            CropVarietyResource::class,
            CropActivityResource::class,
            CropScoutingResource::class,
            HarvestRecordResource::class,
            // Livestock cluster
            LivestockBatchResource::class,
            LivestockHealthRecordResource::class,
            LivestockEventResource::class,
            // Operations cluster
            FarmWorkerResource::class,
            FarmWorkerAttendanceResource::class,
            FarmTaskResource::class,
            FarmEquipmentResource::class,
            FarmWeatherLogResource::class,
            SoilTestRecordResource::class,
            // Finance cluster
            FarmExpenseResource::class,
            FarmBudgetResource::class,
            FarmSaleResource::class,
            FarmProduceInventoryResource::class,
            FarmOrderResource::class,
            FarmCouponResource::class,
            FarmReturnRequestResource::class,
            FarmBundleResource::class,
            FarmSubscriptionResource::class,
            FarmShopBannerResource::class,
            FarmShopNavItemResource::class,
            FarmShopPageResource::class,
            FarmShopBlogPostResource::class,
            FarmB2bAccountResource::class,
            // Phase 3 — new resources
            FarmEquipmentLogResource::class,
            FarmInputChemicalResource::class,
            LivestockBreedingEventResource::class,
            FarmProduceLotResource::class,
            FarmNdviResource::class,
            FarmWeatherAlertResource::class,
            FarmStorageLocationResource::class,
            FarmPostHarvestResource::class,
            // Phase 4 — gap features
            FarmTrialResource::class,
            FarmPastureResource::class,
            FarmInputCreditResource::class,
            FarmInputVoucherResource::class,
            FarmCarbonResource::class,
            FarmSmsCommandResource::class,
        ]);

        // Clusters extend Page — register via pages() so Filament recognises
        // them as navigatable cluster containers in the sidebar.
        $panel->pages([
            CropsCluster::class,
            LivestockCluster::class,
            FarmOperationsCluster::class,
            FarmFinanceCluster::class,
            FarmMarketplaceCluster::class,
            FarmDashboard::class,
            CropYieldReport::class,
            LivestockReport::class,
            FarmFinancialReport::class,
            FarmShopSettingsPage::class,
            // Phase 3 — new pages
            SprayDiaryPage::class,
            LotTraceabilityPage::class,
            AdvancedFarmDashboardPage::class,
            FarmTaskKanbanPage::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }
}
