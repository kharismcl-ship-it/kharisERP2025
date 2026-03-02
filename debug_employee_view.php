<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Modules\HR\Filament\Resources\EmployeeResource\Pages\ViewEmployee;
use Modules\HR\Models\Employee;

// Get employee 1
$employee = Employee::find(1);

if (! $employee) {
    echo "Employee ID 1 not found\n";
    exit;
}

echo 'Employee found: '.$employee->name."\n";

// Try to simulate what happens in the ViewEmployee page
$fieldsToCheck = ['whatsapp_no', 'alt_phone', 'phone', 'emergency_contact_phone'];

foreach ($fieldsToCheck as $field) {
    $value = $employee->$field;
    echo "\nField: $field\n";
    echo 'Type: '.gettype($value)."\n";
    echo 'Value: ';

    if (is_array($value)) {
        echo json_encode($value);
        // Test htmlspecialchars on array
        echo "\nhtmlspecialchars test: ";
        try {
            $result = htmlspecialchars(json_encode($value), ENT_QUOTES, 'UTF-8');
            echo 'Success: '.$result;
        } catch (\Throwable $e) {
            echo 'ERROR: '.$e->getMessage();
        }
    } else {
        var_dump($value);
        echo 'htmlspecialchars test: '.htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }
    echo "\n";
}

// Check if there are any array fields in the employee
$allAttributes = $employee->getAttributes();
echo "\nChecking all attributes for arrays:\n";

foreach ($allAttributes as $key => $value) {
    if (is_array($value)) {
        echo "ARRAY FOUND: $key => ".json_encode($value)."\n";
    }
}
