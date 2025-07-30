<?php

namespace Tests\Feature\Vehicle;

use App\Models\Driver;
use App\Models\PublicUser;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Patoughi\Common\Enums\VehicleStatusEnum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class VehicleDeleteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function test_it_deletes_a_driver_vehicle_successfully(): void
    {
        // Create user with driver
        $user = PublicUser::factory()->create();
        $driver = Driver::factory()->create(['user_id' => $user->id]);

        // Create inactive vehicle assigned to driver
        $vehicle = Vehicle::factory()->create([
            'driver_id' => $driver->id,
            'status' => VehicleStatusEnum::INACTIVE->value,
        ]);
        Passport::actingAs($user);
        $response = $this->deleteJson("api/v1/vehicles/{$vehicle->getHashedId()}");

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'data' => [
                    'message' => __('Driver Vehicle Deleted Successfully'),
                ],
                'success' => true,
            ]);
    }

    /**
     * @return void
     */
    public function test_it_fails_when_user_has_no_driver_profile()
    {
        $user = PublicUser::factory()->create();
        $vehicle = Vehicle::factory()->create();
        Passport::actingAs($user);

        $response = $this->deleteJson("api/v1/vehicles/$vehicle");

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * @return void
     */
    public function test_it_fails_when_driver_not_assigned_to_vehicle(): void
    {
        $user = PublicUser::factory()->create();
        $driver = Driver::factory()->create(['user_id' => $user->id]);

        // Create vehicle assigned to different driver
        $otherDriver = Driver::factory()->create(['user_id' => $user->id]);
        $vehicle = Vehicle::factory()->create([
            'driver_id' => $otherDriver->id,
            'status' => VehicleStatusEnum::INACTIVE->value,
        ]);

        Passport::actingAs($user);
        $response = $this->deleteJson("api/v1/vehicles/{$vehicle->getHashedId()}");

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(['message' => __('Driver Is Not Assign To Vehicle')]);
    }

    /**
     * @return void
     */
    public function test_it_fails_when_vehicle_is_active()
    {
        $user = PublicUser::factory()->create();
        $driver = Driver::factory()->create(['user_id' => $user->id]);

        $vehicle = Vehicle::factory()->create([
            'driver_id' => $driver->id,
            'status' => VehicleStatusEnum::ACTIVE->value,
        ]);

        Passport::actingAs($user);
        $response = $this->deleteJson("api/v1/vehicles/{$vehicle->getHashedId()}");

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(['message' => __('Vehicle Is Active Can Not To Delete')]);
        // Assert vehicle was not deleted
        $this->assertDatabaseHas('vehicles', ['id' => $vehicle->id]);
    }

    /**
     * @return void
     */
    public function test_it_denies_access_for_unauthenticated_users()
    {
        $vehicle = Vehicle::factory()->create();
        $response = $this->deleteJson("api/v1/vehicles/{$vehicle->getHashedId()}");
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
