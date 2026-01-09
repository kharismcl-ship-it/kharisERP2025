<?php

namespace Modules\Hostels\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Hostels\Models\HostelWhatsAppGroup;

class HostelWhatsAppGroupFactory extends Factory
{
    protected $model = HostelWhatsAppGroup::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'group_id' => $this->faker->uuid,
            'is_active' => true,
        ];
    }
}
