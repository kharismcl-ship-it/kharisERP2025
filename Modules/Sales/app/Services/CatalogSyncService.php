<?php

namespace Modules\Sales\Services;

use App\Models\Company;
use Illuminate\Support\Facades\Log;
use Modules\Sales\Models\SalesCatalog;

class CatalogSyncService
{
    public function syncAll(?int $companyId = null): void
    {
        $companies = $companyId
            ? Company::where('id', $companyId)->get()
            : Company::all();

        foreach ($companies as $company) {
            $this->syncWater($company->id);
            $this->syncPaper($company->id);
            $this->syncFarms($company->id);
            $this->syncInventory($company->id);
            $this->syncFleet($company->id);
            $this->syncConstruction($company->id);
            $this->syncHostels($company->id);
        }
    }

    protected function syncWater(int $companyId): void
    {
        if (! class_exists(\Modules\ManufacturingWater\Models\MwPlant::class)) {
            return;
        }

        $plants = \Modules\ManufacturingWater\Models\MwPlant::where('company_id', $companyId)->get();

        foreach ($plants as $plant) {
            SalesCatalog::updateOrCreate(
                [
                    'company_id'    => $companyId,
                    'source_module' => 'ManufacturingWater',
                    'source_type'   => 'MwPlant',
                    'source_id'     => $plant->id,
                ],
                [
                    'name'               => 'Water — ' . $plant->name,
                    'unit'               => 'litre',
                    'base_price'         => $plant->unit_price ?? 0,
                    'availability_mode'  => 'always',
                    'is_active'          => true,
                ]
            );
        }

        Log::info("CatalogSyncService: synced water plants for company {$companyId}");
    }

    protected function syncPaper(int $companyId): void
    {
        if (! class_exists(\Modules\ManufacturingPaper\Models\MpPaperGrade::class)) {
            return;
        }

        $grades = \Modules\ManufacturingPaper\Models\MpPaperGrade::where('company_id', $companyId)
            ->where('is_active', true)
            ->get();

        foreach ($grades as $grade) {
            SalesCatalog::updateOrCreate(
                [
                    'company_id'    => $companyId,
                    'source_module' => 'ManufacturingPaper',
                    'source_type'   => 'MpPaperGrade',
                    'source_id'     => $grade->id,
                ],
                [
                    'name'               => 'Paper — ' . $grade->name . ' (' . $grade->gsm . 'gsm)',
                    'unit'               => 'kg',
                    'base_price'         => $grade->unit_selling_price ?? 0,
                    'availability_mode'  => 'stock',
                    'is_active'          => $grade->is_active,
                ]
            );
        }

        Log::info("CatalogSyncService: synced paper grades for company {$companyId}");
    }

    protected function syncFarms(int $companyId): void
    {
        if (! class_exists(\Modules\Farms\Models\FarmInventory::class)) {
            return;
        }

        $items = \Modules\Farms\Models\FarmInventory::where('company_id', $companyId)
            ->where('quantity', '>', 0)
            ->get();

        foreach ($items as $item) {
            SalesCatalog::updateOrCreate(
                [
                    'company_id'    => $companyId,
                    'source_module' => 'Farms',
                    'source_type'   => 'FarmInventory',
                    'source_id'     => $item->id,
                ],
                [
                    'name'               => $item->name ?? ('Farm Produce #' . $item->id),
                    'unit'               => $item->unit ?? 'kg',
                    'base_price'         => $item->unit_price ?? 0,
                    'availability_mode'  => 'stock',
                    'is_active'          => true,
                ]
            );
        }

        Log::info("CatalogSyncService: synced farm inventory for company {$companyId}");
    }

    protected function syncInventory(int $companyId): void
    {
        if (! class_exists(\Modules\ProcurementInventory\Models\Item::class)) {
            return;
        }

        $items = \Modules\ProcurementInventory\Models\Item::where('company_id', $companyId)
            ->where('is_active', true)
            ->get();

        foreach ($items as $item) {
            SalesCatalog::updateOrCreate(
                [
                    'company_id'    => $companyId,
                    'source_module' => 'ProcurementInventory',
                    'source_type'   => 'Item',
                    'source_id'     => $item->id,
                ],
                [
                    'name'               => $item->name,
                    'sku'                => $item->sku,
                    'unit'               => $item->unit_of_measure ?? 'pcs',
                    'base_price'         => $item->unit_price ?? 0,
                    'availability_mode'  => 'stock',
                    'is_active'          => $item->is_active,
                ]
            );
        }

        Log::info("CatalogSyncService: synced procurement items for company {$companyId}");
    }

    protected function syncFleet(int $companyId): void
    {
        if (! class_exists(\Modules\Fleet\Models\Vehicle::class)) {
            return;
        }

        $vehicles = \Modules\Fleet\Models\Vehicle::where('company_id', $companyId)->get();

        foreach ($vehicles as $vehicle) {
            SalesCatalog::updateOrCreate(
                [
                    'company_id'    => $companyId,
                    'source_module' => 'Fleet',
                    'source_type'   => 'Vehicle',
                    'source_id'     => $vehicle->id,
                ],
                [
                    'name'               => 'Transport — ' . $vehicle->name . ' (' . $vehicle->plate . ')',
                    'unit'               => 'trip',
                    'base_price'         => 0, // set per quotation
                    'availability_mode'  => 'on_request',
                    'is_active'          => true,
                ]
            );
        }

        Log::info("CatalogSyncService: synced fleet vehicles for company {$companyId}");
    }

    protected function syncConstruction(int $companyId): void
    {
        if (! class_exists(\Modules\Construction\Models\ConstructionProject::class)) {
            return;
        }

        // Construction is project-based — add a generic service entry per company
        SalesCatalog::firstOrCreate(
            [
                'company_id'    => $companyId,
                'source_module' => 'Construction',
                'source_type'   => 'ConstructionService',
                'source_id'     => null,
            ],
            [
                'name'               => 'Construction Service',
                'unit'               => 'project',
                'base_price'         => 0,
                'availability_mode'  => 'on_request',
                'is_active'          => true,
            ]
        );

        Log::info("CatalogSyncService: synced construction service for company {$companyId}");
    }

    protected function syncHostels(int $companyId): void
    {
        if (! class_exists(\Modules\Hostels\Models\Hostel::class)) {
            return;
        }

        $hostels = \Modules\Hostels\Models\Hostel::where('company_id', $companyId)->get();

        foreach ($hostels as $hostel) {
            SalesCatalog::updateOrCreate(
                [
                    'company_id'    => $companyId,
                    'source_module' => 'Hostels',
                    'source_type'   => 'Hostel',
                    'source_id'     => $hostel->id,
                ],
                [
                    'name'               => 'Accommodation — ' . $hostel->name,
                    'unit'               => 'night',
                    'base_price'         => 0, // rooms have their own pricing
                    'availability_mode'  => 'on_request',
                    'is_active'          => true,
                ]
            );
        }

        Log::info("CatalogSyncService: synced hostels for company {$companyId}");
    }
}