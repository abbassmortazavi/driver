<?php

namespace Tests\Feature\Driver;

use App\Models\KycLog;
use App\Models\PublicUser;
use App\Repository\KycLog\KycLogRepositoryInterface;
use App\Services\ThirdParties\Contracts\BiometricVerificationInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\Passport;
use Mockery;
use Patoughi\Common\Enums\KycLogStatusEnum;
use Tests\TestCase;

/**
 * Class DriverVerifyBiometricTest
 */
final class DriverVerifyBiometricTest extends TestCase
{
    use RefreshDatabase;

    public function test_driver_biometric_verification_successfully_starts()
    {
        //        Storage::fake('public');

        $user = PublicUser::factory()->create();
        Passport::actingAs($user);

        //        $file = UploadedFile::fake()->create('video.mp4', 5000, 'video/mp4');
        $file = fake()->randomLetter();
        $biometricMock = Mockery::mock(BiometricVerificationInterface::class);
        $biometricMock->shouldReceive('verify')
            ->with($user->id, Mockery::type('string'))
            ->andReturn('mock-track-id-123');
        $this->app->instance(BiometricVerificationInterface::class, $biometricMock);

        $kycMock = Mockery::mock(KycLogRepositoryInterface::class);
        $kycMock->shouldReceive('findByUserIdAndStatus')
            ->with($user->id, KycLogStatusEnum::PENDING)
            ->andReturn(null);
        $this->app->instance(KycLogRepositoryInterface::class, $kycMock);

        $response = $this->postJson(route('drivers.verify.biometric'), [
            'video' => $file,
        ]);

        $response->assertOk();
        $response->assertJsonFragment([
            'message' => trans('messages.processing_video_response_will_be_ready_within_the_next_10_minutes'),
            'track_id' => 'mock-track-id-123',
        ]);

        //        Storage::disk('public')->assertMissing("videos/{$file->hashName()}"); // it should be deleted
    }

    public function test_biometric_verification_fails_when_pending_verification_exists()
    {
        $user = PublicUser::factory()->create();
        Passport::actingAs($user);

        //        $file = UploadedFile::fake()->create('video.mp4', 5000, 'video/mp4');
        $file = fake()->randomLetter();

        $kycLog = KycLog::factory()->create();
        $kycMock = Mockery::mock(KycLogRepositoryInterface::class);
        $kycMock->shouldReceive('findByUserIdAndStatus')
            ->with($user->id, KycLogStatusEnum::PENDING)
            ->andReturn($kycLog);
        $this->app->instance(KycLogRepositoryInterface::class, $kycMock);

        $this->app->instance(BiometricVerificationInterface::class, Mockery::mock(BiometricVerificationInterface::class));

        $response = $this->postJson(route('drivers.verify.biometric'), [
            'video' => $file,
        ]);

        $response->assertUnprocessable();
        //        $response->assertJson([
        //            'message' => trans('messages.you_have_a_pending_biometric_verification_please_wait'),
        //        ]);
    }
}
