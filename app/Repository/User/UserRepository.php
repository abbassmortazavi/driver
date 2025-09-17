<?php

namespace App\Repository\User;

use App\Models\PublicUser;
use Patoughi\Common\Enums\OtpTypeEnum;
use Patoughi\Common\Orm\Repositories\BaseRepository;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(PublicUser $model)
    {
        parent::__construct($model);
    }

    public function findUserWithEmailOrPhone(array $attributes): ?object
    {
        return $this->model::query()
            ->when($attributes['type'] === OtpTypeEnum::SMS->value, fn ($query) => $query->where('phone_number', $attributes['recipient']))
            ->when($attributes['type'] === OtpTypeEnum::EMAIL->value, fn ($query) => $query->where('email', $attributes['recipient']))
            ->first();
    }

    public function findByPhoneNumber(string $phoneNumber): ?PublicUser
    {
        return $this->model::query()
            ->where('phone_number', $phoneNumber)
            ->first();
    }
}
