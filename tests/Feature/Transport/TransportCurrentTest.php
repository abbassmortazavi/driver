<?php

namespace Feature\Transport;

use App\Models\Customer;
use App\Models\Driver;
use App\Models\Order;
use App\Models\PublicUser;
use App\Models\Transport;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Patoughi\Common\Enums\DriverStatusEnum;
use Patoughi\Common\Enums\TransportStatusEnum;
use Patoughi\Common\Enums\VehicleStatusEnum;
use Tests\TestCase;

class TransportCurrentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function test_it_returns_current_transports_for_authenticated_driver()
    {
        // Create a user with driver
        $user = PublicUser::factory()->create();
        $driver = Driver::factory()->create([
            'user_id' => $user->id,
            'status' => DriverStatusEnum::ACTIVE,
        ]);

        // Create transports in different statuses
        $inTransitTransport = Transport::factory()
            ->create([
                'driver_id' => $driver->id,
                'status' => TransportStatusEnum::IN_TRANSIT->value,
            ]);

        Vehicle::factory()->create([
            'driver_id' => $driver->id,
            'status' => VehicleStatusEnum::ACTIVE->value,
        ]);

        $completedTransport = Transport::factory()
            ->create([
                'driver_id' => $driver->id,
                'status' => TransportStatusEnum::COMPLETED->value,
            ]);

        // Create transport for another driver
        $otherDriver = Driver::factory()->create();
        Transport::factory()
            ->create([
                'driver_id' => $otherDriver->id,
                'status' => TransportStatusEnum::IN_TRANSIT->value,
            ]);

        // Act
        Passport::actingAs($user);
        $response = $this->getJson('/api/v1/transports/current');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'status',
                    ],
                ],
            ])
            ->assertJsonCount(1, 'data');
    }

    /**
     * @return void
     */
    public function test_it_includes_required_relationships()
    {
        $user = PublicUser::factory()->create();
        $driver = Driver::factory()->create(['user_id' => $user->id, 'status' => DriverStatusEnum::ACTIVE]);
        $customer = Customer::factory()->create([
            'user_id' => $user->id,
        ]);
        $order = Order::factory()->create([
            'customer_id' => $customer->id,
        ]);

        Vehicle::factory()->create([
            'driver_id' => $driver->id,
            'status' => VehicleStatusEnum::ACTIVE->value,
        ]);

        Transport::factory()
            ->for($driver)
            ->create([
                'status' => TransportStatusEnum::IN_TRANSIT->value,
                'order_id' => $order->id,
            ]);
        Passport::actingAs($user);
        $response = $this->getJson('/api/v1/transports/current');

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'order',
                    'vehicle',
                    'shipments',
                    'vehicle_type',
                    'waybills',
                ],
            ],
        ]);
    }

    /**
     * @return void
     */
    public function test_it_denies_access_for_non_driver_users()
    {
        $user = PublicUser::factory()->create();

        Passport::actingAs($user);
        $response = $this->getJson('/api/v1/transports/current');

        $response->assertForbidden();
    }

    /**
     * @return void
     */
    public function test_it_requires_authentication()
    {
        $response = $this->getJson('/api/v1/transports/current');

        $response->assertUnauthorized();
    }
}
