<?php

namespace App\Models;

use App\Repository\User\UserRepository;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Passport\HasApiTokens;
use Patoughi\Common\Orm\Attributes\Repository;

/**
 * @property-read PublicUser $publicUser
 */
#[Repository(repositoryClass: UserRepository::class)]
class PublicUser extends \Patoughi\Common\Orm\Models\PublicUser
{
    use HasApiTokens;

    public function otpCodes(): HasMany
    {
        return $this->hasMany(OtpCode::class, 'recipient', 'email')
            ->orWhere(function ($query) {
                $query->where('recipient', $this->phone_number);
            });
    }

    /**
     * Find the user instance for the given mobile number
     */
    public function findForPassport($mobile)
    {
        return $this->where('phone_number', $mobile)->first();
    }

    public function driver(): HasOne
    {
        return $this->hasOne(Driver::class);
    }

    public function kycLogs(): HasMany
    {
        return $this->hasMany(KycLog::class);
    }

    public function validateForPassportPasswordGrant(string $password): bool
    {
        return true;
    }
}
