<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Modules\HR\Models\Employee;

// Get employee 1
$employee = Employee::find(1);

if (! $employee) {
    echo "Employee ID 1 not found\n";
    exit;
}

echo 'Employee found: '.$employee->name."\n";

// List all fields that might be used in ViewEmployee
$potentialFields = [
    'whatsapp_no', 'alt_phone', 'phone', 'emergency_contact_phone',
    'email', 'address', 'residential_gps', 'emergency_contact_name',
    'bank_account_holder_name', 'bank_name', 'bank_account_no', 'bank_branch', 'bank_sort_code',
    'hire_date', 'termination_date', 'employment_status', 'employment_type', 'joining_date',
    'system_access_requested', 'system_access_approved_at', 'user_id',
];

echo "\nTesting all potential fields for array values:\n";

echo str_repeat('-', 60)."\n";

foreach ($potentialFields as $field) {
    if (! property_exists($employee, $field)) {
        echo "Field '$field' does not exist\n";

        continue;
    }

    $value = $employee->$field;
    $type = gettype($value);

    echo "Field: $field\n";
    echo "Type: $type\n";
    echo 'Value: ';

    if (is_array($value)) {
        echo 'ARRAY -> '.json_encode($value);
        echo "\nhtmlspecialchars test: ERROR - Cannot pass array to htmlspecialchars\n";
    } elseif (is_object($value)) {
        echo 'OBJECT -> '.get_class($value);
        echo "\nhtmlspecialchars test: ".htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8')."\n";
    } else {
        var_dump($value);
        echo 'htmlspecialchars test: '.htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8')."\n";
    }

    echo str_repeat('-', 60)."\n";
}

// Also check relationships
$relationships = ['company', 'department', 'jobPosition', 'user'];

echo "\nTesting relationships:\n";

echo str_repeat('-', 60)."\n";

foreach ($relationships as $relation) {
    try {
        $related = $employee->$relation;
        echo "Relationship: $relation\n";
        echo 'Type: '.gettype($related)."\n";

        if (is_object($related)) {
            echo 'Class: '.get_class($related)."\n";
            if (method_exists($related, 'getName')) {
                echo 'Name: '.$related->getName()."\n";
            } elseif (property_exists($related, 'name')) {
                echo 'Name: '.$related->name."\n";
            }
        } elseif (is_null($related)) {
            echo "Value: NULL\n";
        } else {
            echo 'Value: '.$related."\n";
        }

        echo str_repeat('-', 60)."\n";
    } catch (\Exception $e) {
        echo "Relationship $relation error: ".$e->getMessage()."\n";
        echo str_repeat('-', 60)."\n";
    }
}
