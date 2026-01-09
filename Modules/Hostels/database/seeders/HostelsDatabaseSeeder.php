<?php

namespace Modules\Hostels\Database\Seeders;

use Illuminate\Database\Seeder;

class HostelsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            HostelSeeder::class,
            HostelBlockSeeder::class,
            HostelFloorSeeder::class,
            RoomSeeder::class,
            BedSeeder::class,
            FeeTypeSeeder::class,
            HostelOccupantSeeder::class,
            BookingSeeder::class,
            HostelChargeSeeder::class,
            BookingChargeSeeder::class,
            HostelBillingCycleSeeder::class,
            HostelBillingRuleSeeder::class,
            HostelUtilityChargeSeeder::class,
            DepositSeeder::class,
            HostelStaffRoleSeeder::class,
            HostelStaffShiftSeeder::class,
            HostelStaffAttendanceSeeder::class,
            HostelHousekeepingScheduleSeeder::class,
            HostelInventoryItemSeeder::class,
            HostelInventoryTransactionSeeder::class,
            RoomInventoryAssignmentSeeder::class,
            MaintenanceRequestSeeder::class,
            MaintenanceRecordSeeder::class,
            IncidentSeeder::class,
            VisitorLogSeeder::class,
            TenantDocumentSeeder::class,
            HostelWhatsAppGroupSeeder::class,
            WhatsAppGroupMessageSeeder::class,
            PricingPolicySeeder::class,
            BookingChangeRequestSeeder::class,
        ]);
    }
}
