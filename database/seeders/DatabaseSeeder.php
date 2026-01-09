<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create main companies
        $kharisGroup = \App\Models\Company::factory()->create([
            'name' => 'Kharis Group',
            'slug' => 'kharis-group',
            'type' => 'holding',
            'is_active' => true,
        ]);

        $kharisHostels = \App\Models\Company::factory()->create([
            'name' => 'Kharis Hostels',
            'slug' => 'kharis-hostels',
            'type' => 'hostel',
            'is_active' => true,
            'parent_company_id' => $kharisGroup->id,
        ]);

        $kharisFarms = \App\Models\Company::factory()->create([
            'name' => 'Kharis Farms',
            'slug' => 'kharis-farms',
            'type' => 'farm',
            'is_active' => true,
            'parent_company_id' => $kharisGroup->id,
        ]);

        // Create super admin user with company assignment
        $superAdmin = User::factory()->withCompany($kharisGroup)->create([
            'name' => 'Super Admin',
            'email' => 'admin@kharis.com',
        ]);

        // Attach to additional companies
        $superAdmin->companies()->attach([
            $kharisHostels->id => ['position' => 'Administrator', 'is_active' => true, 'assigned_at' => now()],
            $kharisFarms->id => ['position' => 'Administrator', 'is_active' => true, 'assigned_at' => now()],
        ]);

        // Assign super admin role
        $superAdminRole = \Spatie\Permission\Models\Role::firstOrCreate(
            ['name' => 'super_admin', 'guard_name' => 'web'],
            ['name' => 'super_admin', 'guard_name' => 'web']
        );
        $superAdmin->assignRole($superAdminRole);

        // Create additional test users
        User::factory(5)->withCompany($kharisHostels)->create();
        User::factory(3)->withCompany($kharisFarms)->create();

        $this->command->info('Database seeded with companies and users!');
        $this->command->info('Super Admin: admin@kharis.com / password');
    }
}
