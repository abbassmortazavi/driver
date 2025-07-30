<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class IranianMobile implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! preg_match('/^(09\d{9}|(\+98|98)\d{10})$/', $value)) {
            $fail(trans('messages.mobile_number_is_invalid'));
        }
    }
}
