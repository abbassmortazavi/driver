<?php

namespace App\Services\ThirdParties;

use App\Exceptions\HooshmandException;
use App\Repository\KycLog\KycLogRepositoryInterface;
use App\Services\ThirdParties\Contracts\VehicleVerificationInterface;
use Exception;
use Illuminate\Support\Facades\Http;
use Patoughi\Common\Enums\KycLogStatusEnum;
use Patoughi\Common\Enums\KycLogTypeEnum;
use Symfony\Component\HttpFoundation\Response;

class HooshmandService implements VehicleVerificationInterface
{
    private $apiKey;

    private $baseUrl;

    public function __construct(private KycLogRepositoryInterface $kycLogRepository)
    {
        $this->apiKey = config('settings.hooshmand.api_key');
        $this->baseUrl = config('settings.hooshmand.base_url');
    }

    /**
     * @param int $userId
     * @param string $nationalCode
     * @param string $plateNumber
     *
     * @return bool
     *
     * @throws HooshmandException
     */
    public function verify(int $userId, string $nationalCode, string $plateNumber): bool
    {
        return true; // todo remove this

        try {
            $response = Http::post($this->baseUrl.'/verify', [
                'national_code' => $nationalCode,
                'plate_number' => $plateNumber,
            ])->json();
            $this->createKycLog($userId, $nationalCode, $plateNumber, $response);

            return $response['success'] ? $response['result']['verified'] : false;

        } catch (Exception $exception) {
            throw new HooshmandException(
                'Failed to verify Vehicle', Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    public function createKycLog(int $userId, string $nationalCode, string $plateNumber, $response = null): void
    {
        $this->kycLogRepository->create([
            'status' => $response['result']['verified'] ? KycLogStatusEnum::VERIFIED : KycLogStatusEnum::REJECTED,
            'type' => KycLogTypeEnum::HOOSHMAND,
            'user_id' => $userId,
            'request_body' => json_encode([
                'national_code' => $nationalCode,
                'plate_number' => $plateNumber,
            ]),
            'response_status_code' => $response->status ?? null,
            'response_message' => $response->message ?? null,
            'response_description' => '',
        ]);
    }
}
