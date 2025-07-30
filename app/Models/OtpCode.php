<?php

namespace App\Models;

use App\Repository\Otp\OtpCodeRepository;
use Patoughi\Common\Orm\Attributes\Repository;

#[Repository(repositoryClass: OtpCodeRepository::class)]
class OtpCode extends \Patoughi\Common\Orm\Models\OtpCode {}
