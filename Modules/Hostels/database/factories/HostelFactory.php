<?php

namespace Modules\Hostels\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Core\Models\Company;
use Modules\Hostels\Models\Hostel;

class HostelFactory extends Factory
{
    protected $model = Hostel::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'slug' => $this->faker->slug,
            'company_id' => Company::factory(),
        ];
    }
}
