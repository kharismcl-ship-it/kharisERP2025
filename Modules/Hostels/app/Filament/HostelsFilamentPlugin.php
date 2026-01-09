<?php

namespace Modules\Hostels\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Modules\Hostels\Filament\Pages\CheckInOutCalendar;
use Modules\Hostels\Filament\Pages\RoomAvailabilityCalendar;
use Modules\Hostels\Filament\Resources\BedResource;
use Modules\Hostels\Filament\Resources\BookingCancellationPolicyResource;
use Modules\Hostels\Filament\Resources\BookingResource;
use Modules\Hostels\Filament\Resources\DepositResource;
use Modules\Hostels\Filament\Resources\FeeTypeResource;
use Modules\Hostels\Filament\Resources\HostelBillingCycleResource;
use Modules\Hostels\Filament\Resources\HostelBillingRuleResource;
use Modules\Hostels\Filament\Resources\HostelBlockResource;
use Modules\Hostels\Filament\Resources\HostelChargeResource;
use Modules\Hostels\Filament\Resources\HostelFloorResource;
use Modules\Hostels\Filament\Resources\HostelHousekeepingResource;
use Modules\Hostels\Filament\Resources\HostelInventoryItemResource;
use Modules\Hostels\Filament\Resources\HostelOccupantResource;
use Modules\Hostels\Filament\Resources\HostelPayrollResource;
use Modules\Hostels\Filament\Resources\HostelResource;
use Modules\Hostels\Filament\Resources\HostelStaffAttendanceResource;
use Modules\Hostels\Filament\Resources\HostelStaffResource;
use Modules\Hostels\Filament\Resources\HostelStaffRoleResource;
use Modules\Hostels\Filament\Resources\HostelTemplateResource;
use Modules\Hostels\Filament\Resources\HostelUtilityChargeResource;
use Modules\Hostels\Filament\Resources\HostelWhatsAppGroupResource;
use Modules\Hostels\Filament\Resources\IncidentResource;
use Modules\Hostels\Filament\Resources\MaintenanceRecordResource;
use Modules\Hostels\Filament\Resources\MaintenanceRequestResource;
use Modules\Hostels\Filament\Resources\PricingPolicyResource;
use Modules\Hostels\Filament\Resources\RoomInventoryAssignmentResource;
use Modules\Hostels\Filament\Resources\RoomResource;
use Modules\Hostels\Filament\Resources\VisitorLogResource;

class HostelsFilamentPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'hostels';
    }

    public function register(Panel $panel): void
    {
        // Split resources between admin and company panels
        if ($panel->getId() === 'admin') {
            // Admin panel resources (HQ/management)
            $panel->resources([
                HostelResource::class,
                HostelTemplateResource::class,
                HostelBlockResource::class,
                HostelFloorResource::class,
                RoomResource::class,
                BedResource::class,
                FeeTypeResource::class,
                HostelBillingRuleResource::class,
                PricingPolicyResource::class,
                BookingCancellationPolicyResource::class,
                HostelStaffRoleResource::class,
                HostelStaffResource::class,
                HostelPayrollResource::class,
                HostelInventoryItemResource::class,
                HostelUtilityChargeResource::class,
                HostelWhatsAppGroupResource::class,
                HostelOccupantResource::class,
                BookingResource::class,
                DepositResource::class,
                HostelBillingCycleResource::class,
                HostelChargeResource::class,
                HostelHousekeepingResource::class,
                MaintenanceRequestResource::class,
                MaintenanceRecordResource::class,
                IncidentResource::class,
                HostelStaffAttendanceResource::class,
                RoomInventoryAssignmentResource::class,
                VisitorLogResource::class,
            ]);
        } elseif ($panel->getId() === 'company-admin') {
            // Company panel resources (operational/tenant-specific)
            $panel->resources([
                HostelOccupantResource::class,
                BookingResource::class,
                DepositResource::class,
                HostelBillingCycleResource::class,
                HostelChargeResource::class,
                HostelHousekeepingResource::class,
                MaintenanceRequestResource::class,
                MaintenanceRecordResource::class,
                IncidentResource::class,
                HostelStaffAttendanceResource::class,
                RoomInventoryAssignmentResource::class,
                VisitorLogResource::class,
            ]);
        }

        // Register pages for both panels
        $panel->pages([
            CheckInOutCalendar::class,
            RoomAvailabilityCalendar::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        // Boot logic here
    }
}
