<?php

namespace App\Services\ThirdParties;

use App\Exceptions\ShahkarException;
use App\Repository\KycLog\KycLogRepositoryInterface;
use App\Services\ThirdParties\Contracts\PhoneNumberVerificationInterface;
use Exception;
use Illuminate\Support\Facades\Http;
use Patoughi\Common\Enums\KycLogStatusEnum;
use Patoughi\Common\Enums\KycLogTypeEnum;
use Symfony\Component\HttpFoundation\Response;

class ShahkarService implements PhoneNumberVerificationInterface
{
    private $apiKey;

    private $baseUrl;

    public function __construct(private readonly KycLogRepositoryInterface $kycLogRepository)
    {
        $this->apiKey = config('settings.shahkar.api_key');
        $this->baseUrl = config('settings.shahkar.base_url');
    }

    /**
     * @throws ShahkarException
     */
    public function verify(int $userId, string $phoneNumber, string $nationalCode): bool
    {
        return true;

        try {
            $response = Http::post($this->baseUrl.'/verify', [
                'phone_number' => $phoneNumber,
                'national_code' => $nationalCode,
            ])->json();
            $this->createKycLog($userId, $phoneNumber, $nationalCode, $response);

            return $response['success'] ? $response['result']['verified'] : false;

        } catch (Exception $exception) {
            throw new ShahkarException(
                'Failed to verify phoneNumber', Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function createKycLog(int $userId, string $phoneNumber, string $nationalCode, $response = null): void
    {
        $this->kycLogRepository->create([
            'status' => $response->status == 200 ? KycLogStatusEnum::VERIFIED : KycLogStatusEnum::REJECTED,
            'type' => KycLogTypeEnum::SHAHKAR,
            'user_id' => $userId,
            'request_body' => json_encode([
                'phone_number' => $phoneNumber,
                'national_code' => $nationalCode,
            ]),
            'response_status_code' => $response->status ?? null,
            'response_message' => $response->message ?? null,
            'response_description' => '',
        ]);
    }
}
