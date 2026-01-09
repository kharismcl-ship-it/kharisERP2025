<?php

namespace Modules\Hostels\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Hostels\Models\Hostel;
use Modules\Hostels\Models\HostelOccupant;

class HostelOccupantFactory extends Factory
{
    protected $model = HostelOccupant::class;

    public function definition(): array
    {
        $firstName = $this->faker->firstName;
        $lastName = $this->faker->lastName;

        return [
            'hostel_id' => Hostel::factory(),
            'first_name' => $firstName,
            'last_name' => $lastName,
            'other_names' => $this->faker->optional()->firstName,
            'full_name' => $firstName.' '.$lastName,
            'gender' => $this->faker->randomElement(['male', 'female']),
            'dob' => $this->faker->optional()->date(),
            'phone' => $this->faker->phoneNumber,
            'alt_phone' => $this->faker->optional()->phoneNumber,
            'email' => $this->faker->unique()->safeEmail,
            'national_id_number' => $this->faker->optional()->numerify('GHA-########'),
            'student_id' => $this->faker->optional()->numerify('STU-####'),
            'institution' => $this->faker->optional()->company,
            'guardian_name' => $this->faker->optional()->name,
            'guardian_phone' => $this->faker->optional()->phoneNumber,
            'guardian_email' => $this->faker->optional()->safeEmail,
            'address' => $this->faker->optional()->address,
            'emergency_contact_name' => $this->faker->optional()->name,
            'emergency_contact_phone' => $this->faker->optional()->phoneNumber,
            'status' => $this->faker->randomElement(['prospect', 'active', 'inactive', 'blacklisted']),
        ];
    }
}
