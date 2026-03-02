<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Modules\HR\Models\Employee;

// Get employee 1 with all attributes
$employee = Employee::find(1);

if (! $employee) {
    echo "Employee ID 1 not found\n";
    exit;
}

echo 'Employee found: '.($employee->name ?? 'No name')."\n";

// Get all attributes
$attributes = $employee->getAttributes();

echo "\nAll attributes:\n";
echo str_repeat('=', 60)."\n";

foreach ($attributes as $key => $value) {
    $type = gettype($value);
    echo "$key: ($type) ";

    if (is_array($value)) {
        echo 'ARRAY -> '.json_encode($value);
    } elseif (is_object($value)) {
        echo 'OBJECT -> '.get_class($value);
    } elseif (is_null($value)) {
        echo 'NULL';
    } else {
        var_dump($value);
    }
    echo "\n";
}

// Check specific fields that might be arrays
echo "\nChecking specific fields:\n";
echo str_repeat('=', 60)."\n";

$fieldsToCheck = ['whatsapp_no', 'alt_phone', 'phone', 'emergency_contact_phone'];

foreach ($fieldsToCheck as $field) {
    if (array_key_exists($field, $attributes)) {
        $value = $attributes[$field];
        $type = gettype($value);

        echo "$field: ($type) ";

        if (is_array($value)) {
            echo 'ARRAY -> '.json_encode($value);
            echo "\n  -> This would cause the htmlspecialchars error!\n";
        } else {
            var_dump($value);
        }
        echo "\n";
    } else {
        echo "$field: NOT IN ATTRIBUTES\n";
    }
}
