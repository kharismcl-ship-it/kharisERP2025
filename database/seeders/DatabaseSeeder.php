<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create main companies (idempotent by slug)
        $kharisGroup = \App\Models\Company::firstOrCreate(
            ['slug' => 'kharis-group'],
            ['name' => 'Kharis Group', 'type' => 'holding', 'is_active' => true]
        );

        $kharisHostels = \App\Models\Company::firstOrCreate(
            ['slug' => 'kharis-hostels'],
            ['name' => 'Kharis Hostels', 'type' => 'hostel', 'is_active' => true, 'parent_company_id' => $kharisGroup->id]
        );

        $kharisFarms = \App\Models\Company::firstOrCreate(
            ['slug' => 'kharis-farms'],
            ['name' => 'Kharis Farms', 'type' => 'farm', 'is_active' => true, 'parent_company_id' => $kharisGroup->id]
        );

        // Create super admin user with company assignment (idempotent by email)
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            ['name' => 'Super Admin', 'password' => 'password', 'email_verified_at' => now()]
        );
        $superAdmin->companies()->syncWithoutDetaching([$kharisGroup->id]);

        // Assign super admin role with company context
        $superAdminRole = \Spatie\Permission\Models\Role::firstOrCreate(
            ['name' => 'super_admin', 'guard_name' => 'web'],
            ['name' => 'super_admin', 'guard_name' => 'web']
        );

        // Assign role with company context using direct database insert (idempotent)
        DB::table('model_has_roles')->updateOrInsert(
            [
                'role_id' => $superAdminRole->id,
                'model_type' => get_class($superAdmin),
                'model_id' => $superAdmin->id,
                'company_id' => $kharisGroup->id,
            ],
            [
                'role_id' => $superAdminRole->id,
                'model_type' => get_class($superAdmin),
                'model_id' => $superAdmin->id,
                'company_id' => $kharisGroup->id,
            ]
        );

        // Create additional test users
        User::factory(5)->withCompany($kharisHostels)->create();
        User::factory(3)->withCompany($kharisFarms)->create();

        $this->command->info('Database seeded with companies and users!');
        $this->command->info('Super Admin: admin@admin.com / password');
    }
}
