<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;

class IranianNationalCode implements ValidationRule
{
    /**
     * Validate the Iranian national code.
     */
    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        // Ensure the value is exactly 10 digits
        if (! preg_match('/^\d{10}$/', $value)) {
            $fail(trans('messages.national_id_is_invalid'));

            return;
        }

        // Split the digits
        $digits = str_split($value);

        // Calculate the checksum
        $check = (int) $digits[9];
        $sum = 0;

        for ($i = 0; $i < 9; $i++) {
            $sum += (int) $digits[$i] * (10 - $i);
        }

        $remainder = $sum % 11;

        if (! ($remainder < 2 && $check == $remainder) && ! ($remainder >= 2 && $check == 11 - $remainder)) {
            $fail(trans('messages.national_id_is_invalid'));
        }
    }
}
