<?php

namespace Modules\Hostels\Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;
use Modules\Hostels\Models\Hostel;

class HostelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $company = Company::firstOrCreate(
            ['slug' => 'kharis-hostels'],
            [
                'name' => 'Kharis Hostels',
                'slug' => 'kharis-hostels',
                'type' => 'hostel',
                'is_active' => true,
            ]
        );

        Hostel::firstOrCreate(
            ['slug' => 'kharis-hostel-east-legon'],
            [
                'company_id' => $company->id,
                'name' => 'Kharis Hostel - East Legon',
                'slug' => 'kharis-hostel-east-legon',
                'code' => 'KH-EAST',
                'location' => 'East Legon',
                'city' => 'Accra',
                'region' => 'Greater Accra',
                'country' => 'Ghana',
                'capacity' => 100,
                'gender_policy' => 'mixed',
                'status' => 'active',
                'notes' => 'Our premium hostel facility located in the heart of East Legon, offering comfortable accommodation for students and travelers.',
            ]
        );

        Hostel::firstOrCreate(
            ['slug' => 'kharis-hostel-adenta'],
            [
                'company_id' => $company->id,
                'name' => 'Kharis Hostel - Adenta',
                'slug' => 'kharis-hostel-adenta',
                'code' => 'KH-ADENTA',
                'location' => 'Adenta',
                'city' => 'Accra',
                'region' => 'Greater Accra',
                'country' => 'Ghana',
                'capacity' => 75,
                'gender_policy' => 'female',
                'status' => 'active',
                'notes' => 'Women-only hostel facility in Adenta, providing a safe and comfortable environment for female students.',
            ]
        );

        Hostel::firstOrCreate(
            ['slug' => 'kharis-hostel-tema'],
            [
                'company_id' => $company->id,
                'name' => 'Kharis Hostel - Tema',
                'slug' => 'kharis-hostel-tema',
                'code' => 'KH-TEMA',
                'location' => 'Tema',
                'city' => 'Tema',
                'region' => 'Greater Accra',
                'country' => 'Ghana',
                'capacity' => 120,
                'gender_policy' => 'male',
                'status' => 'active',
                'notes' => 'Male-only hostel facility in Tema, offering affordable accommodation for male students and travelers.',
            ]
        );
    }
}
