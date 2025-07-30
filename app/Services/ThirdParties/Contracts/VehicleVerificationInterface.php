<?php

namespace App\Services\ThirdParties\Contracts;

interface VehicleVerificationInterface
{
    public function verify(int $userId, string $nationalCode, string $plateNumber): bool;
}
