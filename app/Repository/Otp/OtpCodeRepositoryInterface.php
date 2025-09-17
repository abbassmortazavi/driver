<?php

namespace App\Repository\Otp;

interface OtpCodeRepositoryInterface
{
    /**
     * @param  string  $phoneNumber
     * @return bool
     */
    public function checkPhoneNumberToDelete(string $phoneNumber): bool;

    /**
     * @param  string  $phoneNumber
     * @return mixed
     */
    public function findOtp(string $phoneNumber): mixed;

    /**
     * @param  string  $phone_number
     * @return mixed
     */
    public function lastOtpCode(string $phone_number): mixed;
}
