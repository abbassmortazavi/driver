<?php

namespace Feature\Vehicle;

use App\Models\Driver;
use App\Models\PublicUser;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Vinkla\Hashids\Facades\Hashids;

class VehicleDetailTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function test_it_returns_a_vehicle_resource()
    {
        $user = PublicUser::factory()->create();
        $driver = Driver::factory()->create(['user_id' => $user->id]);
        $vehicle = Vehicle::factory()->create(['driver_id' => $driver->id]);
        Passport::actingAs($user);

        $response = $this->getJson("/api/v1/vehicles/{$vehicle->getHashedId()}");

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'vehicle_type',
                    'plate_number',
                    'color',
                    'status',
                    'fuel_type',
                    'vin_number',
                    'insurance_policy_number',
                    'insurance_expiry_date',
                    'current_location_lat',
                    'current_location_lng',
                    'model_name',
                    'brand_name',
                    'year',
                    'created_at',
                    'updated_at',
                ],
            ]);
    }

    /**
     * @return void
     */
    public function test_it_requires_authentication()
    {
        $vehicle = Vehicle::factory()->create();

        $response = $this->getJson("/api/v1/vehicles/{$vehicle->getHashedId()}");

        $response->assertUnauthorized();
    }
    public function test_it_returns_404_for_nonexistent_vehicle()
    {
        $user = PublicUser::factory()->create();
        $nonExistentId = Hashids::encode(9999);
        Passport::actingAs($user);

        $response = $this
            ->getJson("/api/v1/vehicles/{$nonExistentId}");

        $response->assertNotFound();
    }
}
