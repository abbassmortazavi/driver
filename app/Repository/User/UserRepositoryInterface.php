<?php

namespace App\Repository\User;

use App\Models\PublicUser;

interface UserRepositoryInterface
{
    public function findUserWithEmailOrPhone(array $attributes): mixed;

    public function findByPhoneNumber(string $phoneNumber): ?PublicUser;
}
