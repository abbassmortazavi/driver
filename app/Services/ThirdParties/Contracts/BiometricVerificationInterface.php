<?php

namespace App\Services\ThirdParties\Contracts;

interface BiometricVerificationInterface
{
    public function verify(int $userId, string $videoPath): string;

    public function checkVerify(string $trackId): bool;
}
