<?php

namespace Tests\Feature\Transport;

use App\Models\Driver;
use App\Models\Order;
use App\Models\PublicUser;
use App\Models\Shipment;
use App\Models\Transport;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Laravel\Passport\Passport;
use Patoughi\Common\Enums\DriverStatusEnum;
use Patoughi\Common\Enums\TransportStatusEnum;
use Patoughi\Common\Enums\VehicleStatusEnum;
use Tests\TestCase;

class TransportAvailableTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function test_it_returns_available_transports_for_authenticated_driver()
    {
        Event::fake();
        $user = PublicUser::factory()->create();
        $driver = Driver::factory()->create([
            'user_id' => $user->id,
            'status' => DriverStatusEnum::ACTIVE->value,
            'identity_verified_at' => now(),
        ]);
        Vehicle::factory()->create([
            'driver_id' => $driver->id,
            'status' => VehicleStatusEnum::ACTIVE->value,
        ]);
        Passport::actingAs($user);

        Transport::factory()
            ->has(Order::factory()->has(Shipment::factory()))
            ->create([
                'driver_id' => $driver->id,
                'status' => TransportStatusEnum::PENDING->value,
            ]);

        $response = $this->getJson('/api/v1/transports/available');

        $response->assertOk()->assertJsonCount(1, 'data');
    }

    /**
     * @return void
     */
    public function test_it_denies_access_for_unauthenticated_users()
    {
        $response = $this->getJson('/api/v1/transports/available');
        $response->assertUnauthorized();
    }
}
