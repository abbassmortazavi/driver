<?php

namespace Tests\Feature\Driver;

use App\Models\Driver;
use App\Models\PublicUser;
use App\Repository\Driver\DriverRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Mockery;

class DriverUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_driver_successfully(): void
    {
        $user = PublicUser::factory()->create();
        $driver = Driver::factory()->create(['user_id' => $user->id]);
        Passport::actingAs($user);

        $updateData = [
            'licence_number' => 'XYZ123456',
            'licence_type' => 'B2',
            'licence_expired_at' => now()->addYear()->format('Y-m-d'),
            'emergency_contact_name' => 'Ali Rezaei',
            'emergency_contact_phone' => '09121234567',
        ];

        $response = $this->putJson(route('drivers.update', ['driver' => $driver->getHashedId()]), $updateData);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            'licence_number' => 'XYZ123456',
            'licence_type' => 'B2',
        ]);
    }

    public function test_update_driver_validation_error(): void
    {
        $user = PublicUser::factory()->create();
        $driver = Driver::factory()->create(['user_id' => $user->id]);
        Passport::actingAs($user);

        $invalidData = [
            'licence_number' => '',
            'licence_type' => '',
            'licence_expired_at' => 'not-a-date',
        ];

        $response = $this->putJson(route('drivers.update', ['driver' => $driver->getHashedId()]), $invalidData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors([
            'licence_number',
            'licence_type',
            'licence_expired_at',
        ]);
    }

    public function test_update_driver_fails_with_exception(): void
    {
        $user = PublicUser::factory()->create();
        $driver = Driver::factory()->create(['user_id' => $user->id]);
        Passport::actingAs($user);

        $updateData = [
            'licence_number' => 'XYZ123456',
            'licence_type' => 'B2',
            'licence_expired_at' => now()->addYear()->format('Y-m-d'),
            'emergency_contact_name' => 'Ali Rezaei',
            'emergency_contact_phone' => '09121234567',
        ];

        $mockRepository = Mockery::mock(DriverRepositoryInterface::class);
        $mockRepository->shouldReceive('update')
            ->once()
            ->andThrow(new \Exception('Update error', 500));

        $this->app->instance(DriverRepositoryInterface::class, $mockRepository);

        $response = $this->putJson(route('drivers.update', ['driver' => $driver->getHashedId()]), $updateData);

        $response->assertStatus(500);
        $response->assertJsonFragment([
            'message' => 'Update error',
        ]);
    }
}
