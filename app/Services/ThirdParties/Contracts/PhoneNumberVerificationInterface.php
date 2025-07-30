<?php

namespace App\Services\ThirdParties\Contracts;

interface PhoneNumberVerificationInterface
{
    public function verify(int $userId, string $phoneNumber, string $nationalCode): bool;
}
