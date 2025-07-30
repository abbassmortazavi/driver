<?php

namespace Tests\Feature\Driver;

use App\Models\Driver;
use App\Models\KycLog;
use App\Models\PublicUser;
use App\Repository\Driver\DriverRepositoryInterface;
use App\Repository\KycLog\KycLogRepositoryInterface;
use App\Services\ThirdParties\Contracts\BiometricVerificationInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Mockery;
use Patoughi\Common\Enums\KycLogStatusEnum;
use Tests\TestCase;

/**
 * Class DriverCheckVerifyBiometricTest
 */
final class DriverCheckVerifyBiometricTest extends TestCase
{
    use RefreshDatabase;

    public function test_check_verify_biometric_returns_verified_message_when_already_verified()
    {
        $user = PublicUser::factory()->has(Driver::factory())->create();
        Passport::actingAs($user);

        $kycLog = KycLog::factory()->create([
            'status' => KycLogStatusEnum::VERIFIED,
        ]);

        $kycRepo = Mockery::mock(KycLogRepositoryInterface::class);
        $kycRepo->shouldReceive('findByUserIdAndTrackId')->andReturn($kycLog);
        $this->app->instance(KycLogRepositoryInterface::class, $kycRepo);

        $this->mock(BiometricVerificationInterface::class);
        $this->mock(DriverRepositoryInterface::class);

        $response = $this->getJson(route('drivers.check.verify.biometric', [
            'id' => $kycLog->track_id,
        ]));

        $response->assertOk();
        $response->assertJsonFragment([
            'message' => trans('messages.your_biometric_validation_was_successful'),
        ]);
    }

    public function test_check_verify_biometric_returns_rejected_message_when_already_rejected()
    {
        $user = PublicUser::factory()->has(Driver::factory())->create();
        Passport::actingAs($user);

        $kycLog = KycLog::factory()->create([
            'status' => KycLogStatusEnum::REJECTED,
        ]);

        $kycRepo = Mockery::mock(KycLogRepositoryInterface::class);
        $kycRepo->shouldReceive('findByUserIdAndTrackId')->andReturn($kycLog);
        $this->app->instance(KycLogRepositoryInterface::class, $kycRepo);

        $this->mock(BiometricVerificationInterface::class);
        $this->mock(DriverRepositoryInterface::class);

        $response = $this->getJson(route('drivers.check.verify.biometric', [
            'id' => $kycLog->track_id,
        ]));

        $response->assertOk();
        $response->assertJsonFragment([
            'message' => trans('messages.your_biometric_validation_encountered_an_error'),
        ]);
    }

    public function test_check_verify_biometric_runs_successful_verification()
    {
        $user = PublicUser::factory()->has(Driver::factory())->create();
        Passport::actingAs($user);

        $kycLog = KycLog::factory()->create([
            'status' => KycLogStatusEnum::PENDING,
        ]);

        $kycRepo = Mockery::mock(KycLogRepositoryInterface::class);
        $kycRepo->shouldReceive('findByUserIdAndTrackId')->andReturn($kycLog);
        $kycRepo->shouldReceive('update')->once();

        $this->app->instance(KycLogRepositoryInterface::class, $kycRepo);

        $driverRepo = Mockery::mock(DriverRepositoryInterface::class);
        $driverRepo->shouldReceive('update')->once();
        $this->app->instance(DriverRepositoryInterface::class, $driverRepo);

        $biometric = Mockery::mock(BiometricVerificationInterface::class);
        $biometric->shouldReceive('checkVerify')->andReturnTrue();
        $this->app->instance(BiometricVerificationInterface::class, $biometric);

        $response = $this->getJson(route('drivers.check.verify.biometric', [
            'id' => $kycLog->track_id,
        ]));

        $response->assertOk();
        $response->assertJsonFragment([
            'message' => trans('messages.your_biometric_validation_was_successful'),
        ]);
    }

    public function test_check_verify_biometric_returns_error_if_kyc_log_not_found()
    {
        $user = PublicUser::factory()->has(Driver::factory())->create();
        Passport::actingAs($user);

        $kycRepo = Mockery::mock(KycLogRepositoryInterface::class);
        $kycRepo->shouldReceive('findByUserIdAndTrackId')->andReturn(null);
        $this->app->instance(KycLogRepositoryInterface::class, $kycRepo);

        $this->mock(DriverRepositoryInterface::class);
        $this->mock(BiometricVerificationInterface::class);

        $response = $this->getJson(route('drivers.check.verify.biometric', [
            'id' => uniqid(),
        ]));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment([
            'message' => trans('messages.kyc_log_not_found'),
        ]);
    }
}
