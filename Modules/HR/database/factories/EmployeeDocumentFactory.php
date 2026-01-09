<?php

namespace Modules\HR\Database\factories;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\HR\Models\Employee;
use Modules\HR\Models\EmployeeDocument;

class EmployeeDocumentFactory extends Factory
{
    protected $model = EmployeeDocument::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'employee_id' => Employee::factory(),
            'document_type' => $this->faker->randomElement(['CV', 'ID', 'CERTIFICATE', 'CONTRACT']),
            'file_path' => $this->faker->filePath(),
            'uploaded_by_user_id' => User::factory(),
            'description' => $this->faker->optional()->sentence(),
        ];
    }
}
