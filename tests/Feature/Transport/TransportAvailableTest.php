<?php

namespace Tests\Feature\Transport;

use App\Models\Bid;
use App\Models\Driver;
use App\Models\PublicUser;
use App\Models\Transport;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;
use Patoughi\Common\Enums\DriverStatusEnum;
use Patoughi\Common\Enums\TransportAssignmentStatusEnum;
use Patoughi\Common\Enums\TransportStatusEnum;
use Tests\TestCase;

class TransportAvailableTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function test_it_returns_available_transports_for_authenticated_driver()
    {
        // Create a driver user
        $user = PublicUser::factory()->create();
        $driver = Driver::factory()->create(['user_id' => $user->id, 'status' => DriverStatusEnum::ACTIVE, 'identity_verified_at' => now()]);

        // Create available transports
        $availableTransports = Transport::factory()
            ->count(3)
            ->create([
                'status' => TransportStatusEnum::PENDING->value,
                'driver_id' => null,
                'assignment_status' => TransportAssignmentStatusEnum::UNASSIGNED->value,
            ]);

        // Create some unavailable transports (should not appear in results)
        Transport::factory()->create(['status' => TransportStatusEnum::COMPLETED->value]);
        Transport::factory()->create(['driver_id' => $driver->id]);
        Transport::factory()->create(['assignment_status' => TransportAssignmentStatusEnum::ASSIGNED->value]);

        Vehicle::factory()->create(['driver_id' => $driver->id, 'status' => 'active']);

        // Create a transport the driver has already bid on
        $bidTransport = Transport::factory()->create([
            'status' => TransportStatusEnum::PENDING->value,
            'driver_id' => null,
            'assignment_status' => TransportAssignmentStatusEnum::UNASSIGNED->value,
        ]);

        Passport::actingAs($user);
        Gate::shouldReceive('authorize')->once()->andReturn(true);

        $response = $this->getJson('/api/v1/transports/available?per_page=2');

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'origin',
                        'destination',
                        'date',
                        'price',
                        'negotiation_allowed',
                        'allowed_to_bid',
                    ],
                ],
                'meta' => [
                    'current_page',
                    'per_page',
                    'total',
                ],
            ]);
    }

    /**
     * @return void
     */
    public function test_it_denies_access_for_unauthenticated_users()
    {
        $response = $this->getJson('/api/v1/transports/available');
        $response->assertUnauthorized();
    }

    /**
     * @return void
     */
    public function test_it_paginates_results_correctly()
    {
        $user = PublicUser::factory()->create();
        $driver = Driver::factory()->create(['user_id' => $user->id, 'status' => DriverStatusEnum::ACTIVE, 'identity_verified_at' => now()]);
        Vehicle::factory()->create(['driver_id' => $driver->id, 'status' => 'active']);

        Transport::factory()
            ->count(15)
            ->create([
                'status' => TransportStatusEnum::PENDING->value,
                'driver_id' => null,
                'assignment_status' => TransportAssignmentStatusEnum::UNASSIGNED->value,
            ]);

        // First page
        Passport::actingAs($user);
        $response = $this->getJson('/api/v1/transports/available?per_page=10');

        $response->assertOk()
            ->assertJsonPath('meta.current_page', 1)
            ->assertJsonPath('meta.per_page', 10)
            ->assertJsonPath('meta.total', 15)
            ->assertJsonCount(10, 'data');

        // Second page
        $response = $this->actingAs($user)
            ->getJson('/api/v1/transports/available?per_page=10&page=2');

        $response->assertOk()
            ->assertJsonPath('meta.current_page', 2)
            ->assertJsonCount(5, 'data');

    }
}
