<?php

namespace Tests\Feature\Driver;

use App\Models\Driver;
use App\Models\PublicUser;
use App\Repository\Driver\DriverRepositoryInterface;
use App\Services\ThirdParties\Contracts\PhoneNumberVerificationInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Mockery;
use Tests\TestCase;

/**
 * Class DriverStoreTest
 */
final class DriverStoreTest extends TestCase
{
    use RefreshDatabase;
    use RefreshDatabase;

    public function test_store_driver_successfully(): void
    {
        $user = PublicUser::factory()->create();
        Passport::actingAs($user);

        $requestData = [
            'first_name' => 'Ali',
            'last_name' => 'Ahmadi',
            'national_code' => '4310924336',
            'birth_date' => '1370-01-01',
            'gender' => 'male',
        ];

        $mockVerification = Mockery::mock(PhoneNumberVerificationInterface::class);
        $mockVerification->shouldReceive('verify')
            ->once()
            ->with($user->id, $user->phone_number, $requestData['national_code'])
            ->andReturn(true);

        $mockRepository = Mockery::mock(DriverRepositoryInterface::class);
        $mockRepository->shouldReceive('updateOrCreate')
            ->once()
            ->andReturn(new Driver($requestData));

        $this->app->instance(PhoneNumberVerificationInterface::class, $mockVerification);
        $this->app->instance(DriverRepositoryInterface::class, $mockRepository);

        $response = $this->postJson(route('drivers.store'), $requestData);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure(['data']);
    }

    public function test_store_fails_if_driver_already_verified(): void
    {
        $user = PublicUser::factory()->create();
        Passport::actingAs($user);

        Driver::factory()->create([
            'user_id' => $user->id,
            'identity_verified_at' => now(),
        ]);

        $requestData = [
            'first_name' => 'Ali',
            'last_name' => 'Ahmadi',
            'national_code' => '4310924336',
            'birth_date' => '1370-01-01',
            'gender' => 'male',
        ];

        $mockVerification = Mockery::mock(PhoneNumberVerificationInterface::class);
        $this->app->instance(PhoneNumberVerificationInterface::class, $mockVerification);

        $mockRepository = Mockery::mock(DriverRepositoryInterface::class);
        $this->app->instance(DriverRepositoryInterface::class, $mockRepository);

        $response = $this->postJson(route('drivers.store'), $requestData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(['message' => trans('messages.driver_already_exist')]);
    }

    public function test_store_fails_if_phone_verification_fails(): void
    {
        $user = PublicUser::factory()->create();
        Passport::actingAs($user);

        $requestData = [
            'first_name' => 'Ali',
            'last_name' => 'Ahmadi',
            'national_code' => '4310924336',
            'birth_date' => '1370-01-01',
            'gender' => 'male',
        ];

        $mockVerification = Mockery::mock(PhoneNumberVerificationInterface::class);
        $mockVerification->shouldReceive('verify')
            ->once()
            ->andReturn(false);

        $mockRepository = Mockery::mock(DriverRepositoryInterface::class);
        $this->app->instance(PhoneNumberVerificationInterface::class, $mockVerification);
        $this->app->instance(DriverRepositoryInterface::class, $mockRepository);

        $response = $this->postJson(route('drivers.store'), $requestData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJson([
            'message' => trans('messages.national_code_and_mobile_number_do_not_match'),
            'errors' => [
                'national_code' => [
                    trans('messages.national_code_and_mobile_number_do_not_match'),
                ],
            ],
        ]);
    }

    public function test_store_handles_general_exception(): void
    {
        $user = PublicUser::factory()->create();
        Passport::actingAs($user);

        $requestData = [
            'first_name' => 'Ali',
            'last_name' => 'Ahmadi',
            'national_code' => '4310924336',
            'birth_date' => '1370-01-01',
            'gender' => 'male',
        ];

        $mockVerification = Mockery::mock(PhoneNumberVerificationInterface::class);
        $mockVerification->shouldReceive('verify')->andReturn(true);

        $mockRepository = Mockery::mock(DriverRepositoryInterface::class);
        $mockRepository->shouldReceive('updateOrCreate')
            ->andThrow(new \Exception('Something went wrong', 500));

        $this->app->instance(PhoneNumberVerificationInterface::class, $mockVerification);
        $this->app->instance(DriverRepositoryInterface::class, $mockRepository);

        $response = $this->postJson(route('drivers.store'), $requestData);

        $response->assertStatus(500);
        $response->assertJsonFragment([
            'message' => 'Something went wrong',
        ]);
    }
}
