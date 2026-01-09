<?php

namespace Modules\Hostels\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Hostels\Models\HostelOccupant;
use Modules\Hostels\Models\HostelOccupantDocument;

class HostelOccupantDocumentFactory extends Factory
{
    protected $model = HostelOccupantDocument::class;

    public function definition(): array
    {
        return [
            'hostel_occupant_id' => HostelOccupant::factory(),
            'document_type' => $this->faker->randomElement(['id_card', 'passport', 'student_id', 'contract', 'other']),
            'file_path' => $this->faker->filePath(),
            'uploaded_by' => User::factory(),
        ];
    }
}
