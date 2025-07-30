<?php

namespace App\Services\ThirdParties;

use App\Exceptions\BiometricException;
use App\Repository\KycLog\KycLogRepositoryInterface;
use App\Services\ThirdParties\Contracts\BiometricVerificationInterface;
use Exception;
use Illuminate\Support\Facades\Http;
use Patoughi\Common\Enums\KycLogStatusEnum;
use Patoughi\Common\Enums\KycLogTypeEnum;
use Symfony\Component\HttpFoundation\Response;

class BiometricService implements BiometricVerificationInterface
{
    private $apiKey;

    private $baseUrl;

    public function __construct(private readonly KycLogRepositoryInterface $kycLogRepository)
    {
        $this->apiKey = config('settings.biometric.api_key');
        $this->baseUrl = config('settings.biometric.base_url');
    }

    /**
     * @throws BiometricException
     */
    public function verify(int $userId, string $videoPath): string
    {
        $response['track_id'] = uniqid(); // todo remove this
        $this->createKycLog($userId, $response);

        return $response['track_id'];

        try {
            $response = Http::attach(
                'video', file_get_contents($videoPath), basename($videoPath)
            )->post($this->baseUrl.'/verify', [
                'user_id' => $userId,
            ]);
            $this->createKycLog($userId, $response);

            return $response['track_id'];

        } catch (Exception $exception) {
            throw new BiometricException(
                'Failed to verify biometric', Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * @throws BiometricException
     */
    public function checkVerify(string $trackId): bool
    {
        return true; // todo remove this

        try {
            $response = Http::post($this->baseUrl.'/check-verify', [
                'track_id' => $trackId,
            ])->json();

            return $response['success'] ? $response['result']['verified'] : false;

        } catch (Exception $exception) {
            throw new BiometricException(
                'Failed to check verify biometric', Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * @param  null  $response
     */
    public function createKycLog(int $userId, $response = null): void
    {
        $this->kycLogRepository->create([
            'status' => KycLogStatusEnum::PENDING,
            'type' => KycLogTypeEnum::BIOMETRIC,
            'user_id' => $userId,
            'track_id' => $response['track_id'],
            'response_status_code' => $response['status'] ?? null,
            'response_message' => $response['message'] ?? null,
            'response_description' => '',
        ]);
    }
}
