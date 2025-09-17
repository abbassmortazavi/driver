<?php

namespace Tests\Feature\Vehicle;

use App\Models\Driver;
use App\Models\PublicUser;
use App\Models\Vehicle;
use App\Models\VehicleType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Patoughi\Common\Enums\VehicleFuelTypeEnum;
use Patoughi\Common\Enums\VehicleStatusEnum;
use Tests\TestCase;
use Vinkla\Hashids\Facades\Hashids;

class VehicleUpdateTest extends TestCase
{
    use RefreshDatabase;

    private PublicUser $user;

    private Driver $driver;

    private Vehicle $vehicle;

    private VehicleType $vehicleType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = PublicUser::factory()->create();
        $this->driver = Driver::factory()->create(['user_id' => $this->user->id]);
        $this->vehicleType = VehicleType::factory()->create();
        $this->vehicle = Vehicle::factory()->create([
            'driver_id' => $this->driver->id,
            'vehicle_type_id' => $this->vehicleType->id,
            'status' => VehicleStatusEnum::ACTIVE,
        ]);

        Passport::actingAs($this->user);
    }

    public function test_vehicle_update_validates_field_formats()
    {
        $response = $this->putJson("/api/v1/vehicles/{$this->vehicle->getHashedId()}", [
            'name' => 123, // Should be string
            'plate_number' => str_repeat('a', 21),
            'vin_number' => str_repeat('a', 18),
            'insurance_policy_number' => str_repeat('a', 51),
            'color' => str_repeat('a', 31),
            'model_name' => str_repeat('a', 51),
            'brand_name' => str_repeat('a', 51),
            'year' => '12345',
            'fuel_type' => 'INVALID_TYPE',
            'insurance_expiry_date' => 'invalid-date',
            'vehicle_type_id' => 'non-existent-id',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors([
                'name',
                'plate_number',
                'vin_number',
                'insurance_policy_number',
                'color',
                'model_name',
                'brand_name',
                'year',
                'fuel_type',
                'insurance_expiry_date',
                'vehicle_type_id',
            ]);
    }

    public function test_vehicle_update_accepts_valid_data()
    {
        $response = $this->putJson("/api/v1/vehicles/{$this->vehicle->getHashedId()}", [
            'name' => 'Test Vehicle',
            'plate_number' => 'ABC123',
            'vin_number' => '12345678901234567',
            'insurance_policy_number' => 'POLICY123',
            'color' => 'Red',
            'model_name' => 'Model X',
            'brand_name' => 'Brand Y',
            'year' => '2023',
            'fuel_type' => VehicleFuelTypeEnum::GASOLINE->value,
            'insurance_expiry_date' => now()->addYear()->format('Y-m-d'),
            'vehicle_type_id' => Hashids::encode($this->vehicleType->id),
        ]);

        $response->assertOk();
    }

    public function test_vehicle_not_found_returns_404()
    {
        $response = $this->putJson('/api/v1/vehicles/non-existent', [
            'name' => 'Updated Name',
        ]);

        $response->assertNotFound();
    }

    public function test_unauthenticated_user_cannot_update_vehicle()
    {
        $this->app['auth']->forgetGuards();

        $response = $this->putJson("/api/v1/vehicles/{$this->vehicle->getHashedId()}", [
            'name' => 'Updated Name',
        ]);

        $response->assertUnauthorized();
    }
}
