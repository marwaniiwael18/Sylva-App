<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

// Test the validation rules
$request = new Request();
$request->merge(['refund_reason' => 'bad']); // Short reason

$validator = Validator::make($request->all(), [
    'refund_reason' => 'required|string|min:10|max:500'
]);

if ($validator->fails()) {
    echo "Short reason validation failed:\n";
    foreach ($validator->errors()->all() as $error) {
        echo "- $error\n";
    }
} else {
    echo "Short reason validation passed unexpectedly\n";
}

echo "\n";

$request2 = new Request();
$request2->merge(['refund_reason' => 'This is a valid refund reason with more than 10 characters']); // Valid reason

$validator2 = Validator::make($request2->all(), [
    'refund_reason' => 'required|string|min:10|max:500'
]);

if ($validator2->fails()) {
    echo "Valid reason validation failed:\n";
    foreach ($validator2->errors()->all() as $error) {
        echo "- $error\n";
    }
} else {
    echo "Valid reason validation passed\n";
}