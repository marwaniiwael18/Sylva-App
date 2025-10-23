<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NotNumericOnly implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Check if the value contains only numeric characters (including decimals and negative signs)
        if (is_numeric($value)) {
            $fail('The :attribute cannot contain only numbers. Please add some text.');
        }
        
        // Also check if it's only spaces and numbers
        if (preg_match('/^[\d\s\.\-\+]+$/', $value)) {
            $fail('The :attribute cannot contain only numbers. Please add some text.');
        }
    }
}
