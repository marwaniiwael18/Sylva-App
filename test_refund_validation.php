<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\DonationController;

// Simulate a refund request with insufficient reason length
$request = new Request();
$request->merge([
    'refund_reason' => 'bad' // Only 3 characters, should fail validation
]);

// Create validator with the same rules as the controller
$validator = Validator::make($request->all(), [
    'refund_reason' => 'required|string|min:10'
]);

if ($validator->fails()) {
    echo "Validation failed as expected:\n";
    foreach ($validator->errors()->all() as $error) {
        echo "- $error\n";
    }
} else {
    echo "Validation passed unexpectedly\n";
}

echo "\nTesting with valid reason:\n";

$request2 = new Request();
$request2->merge([
    'refund_reason' => 'This is a valid refund reason with more than 10 characters'
]);

$validator2 = Validator::make($request2->all(), [
    'refund_reason' => 'required|string|min:10'
]);

if ($validator2->fails()) {
    echo "Validation failed unexpectedly:\n";
    foreach ($validator2->errors()->all() as $error) {
        echo "- $error\n";
    }
} else {
    echo "Validation passed as expected\n";
}