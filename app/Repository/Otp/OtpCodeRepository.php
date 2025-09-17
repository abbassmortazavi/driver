<?php

namespace App\Repository\Otp;

use App\Models\OtpCode;
use Patoughi\Common\Orm\Repositories\BaseRepository;

class OtpCodeRepository extends BaseRepository implements OtpCodeRepositoryInterface
{
    public function __construct(OtpCode $model)
    {
        parent::__construct($model);
    }

    public function checkPhoneNumberToDelete(string $phoneNumber): bool
    {
        $res = $this->model->query()->where('recipient', $phoneNumber);
        if ($res->exists()) {
            $res->delete();
        }

        return true;
    }

    /**
     * @param  string  $phoneNumber
     * @return mixed
     */
    public function findOtp(string $phoneNumber): mixed
    {
        return $this->model->query()->where('recipient', $phoneNumber)->first();
    }

    /**
     * @param  string  $phone_number
     * @return mixed
     */
    public function lastOtpCode(string $phone_number): mixed
    {
        return $this->model->query()->where('recipient', $phone_number)->latest()->first();
    }
}
