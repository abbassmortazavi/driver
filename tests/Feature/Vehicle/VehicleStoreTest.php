<?php

namespace Tests\Feature\Vehicle;

use App\Models\Driver;
use App\Models\PublicUser;
use App\Models\Vehicle;
use App\Models\VehicleType;
use App\Repository\DriverVehicleAssignmentHistory\DriverVehicleAssignmentHistoryRepositoryInterface;
use App\Repository\Vehicle\VehicleRepositoryInterface;
use App\Services\ThirdParties\Contracts\VehicleVerificationInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Mockery;
use Patoughi\Common\Enums\VehicleFuelTypeEnum;
use Tests\TestCase;
use Vinkla\Hashids\Facades\Hashids;

/**
 * Class VehicleStoreTest
 */
final class VehicleStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_driver_vehicle_store_successfully_creates_vehicle()
    {
        $user = PublicUser::factory()->has(Driver::factory())->create();
        Driver::factory()->create(['user_id' => $user->id]);
        Passport::actingAs($user);

        $this->mock(VehicleVerificationInterface::class)
            ->shouldReceive('verify')
            ->once()
            ->with($user->id, $user->driver->national_code, '12الف34567')
            ->andReturn(true);

        $vehicle = Vehicle::factory()->create();

        $vehicleRepo = Mockery::mock(VehicleRepositoryInterface::class);
        $vehicleRepo->shouldReceive('create')->once()->andReturn($vehicle);
        $this->app->instance(VehicleRepositoryInterface::class, $vehicleRepo);

        $historyRepo = Mockery::mock(DriverVehicleAssignmentHistoryRepositoryInterface::class);
        $historyRepo->shouldReceive('create')->once();
        $this->app->instance(DriverVehicleAssignmentHistoryRepositoryInterface::class, $historyRepo);

        $payload = [
            'name' => fake()->name(),
            'vehicle_type_id' => Hashids::encode(VehicleType::factory()->create()->id),
            'fuel_type' => fake()->randomElement(array_column(VehicleFuelTypeEnum::cases(), 'value')),
            'plate_number' => '12الف34567',
            'vin_number' => (string) fake()->randomDigit(),
            'insurance_policy_number' => (string) fake()->randomDigit(),
            'insurance_expiry_date' => fake()->date(),
            'color' => fake()->colorName(),
            'model_name' => fake()->randomLetter(),
            'brand_name' => fake()->randomLetter(),
            'year' => (string) fake()->randomDigit(),
        ];

        $response = $this->postJson(route('vehicles.store'), $payload);
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                [
                    'id',
                    'name',
                    'vehicle_type',
                    'status',
                    'fuel_type',
                    'plate_number',
                    'vin_number',
                    'insurance_policy_number',
                    'insurance_expiry_date',
                    'color',
                    'model_name',
                    'brand_name',
                    'year',
                ],
            ],
        ]);
    }

    public function test_driver_vehicle_store_fails_when_verification_fails()
    {
        $user = PublicUser::factory()->has(Driver::factory())->create();
        Driver::factory()->create(['user_id' => $user->id]);
        Passport::actingAs($user);

        $this->mock(VehicleVerificationInterface::class)
            ->shouldReceive('verify')
            ->once()
            ->with($user->id, $user->driver->national_code, '12الف34567')
            ->andReturn(false);

        $payload = [
            'name' => fake()->name(),
            'vehicle_type_id' => Hashids::encode(VehicleType::factory()->create()->id),
            'fuel_type' => fake()->randomElement(array_column(VehicleFuelTypeEnum::cases(), 'value')),
            'plate_number' => '12الف34567',
            'vin_number' => (string) fake()->randomDigit(),
            'insurance_policy_number' => (string) fake()->randomDigit(),
            'insurance_expiry_date' => fake()->date(),
            'color' => fake()->colorName(),
            'model_name' => fake()->randomLetter(),
            'brand_name' => fake()->randomLetter(),
            'year' => (string) fake()->randomDigit(),
        ];

        $response = $this->postJson(route('vehicles.store'), $payload);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJson([
            'message' => trans('messages.national_code_and_plate_number_do_not_match'),
            'errors' => [
                'national_code' => [
                    trans('messages.national_code_and_plate_number_do_not_match'),
                ],
            ],
        ]);
    }
}
