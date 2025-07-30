<?php

namespace Tests\Feature\Transport;

use App\Models\Driver;
use App\Models\PublicUser;
use App\Models\Transport;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Patoughi\Common\Enums\TransportAssignmentStatusEnum;
use Patoughi\Common\Enums\TransportStatusEnum;
use Tests\TestCase;

class TransportListAssignToDriverTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_assigned_transports_for_authenticated_driver()
    {
        $user = PublicUser::factory()->create();
        $driver = Driver::factory()->create(['user_id' => $user->id]);
        $vehicle = Vehicle::factory()->create(['driver_id' => $driver->id]);

        Transport::factory()->count(3)->create([
            'driver_id' => $driver->id,
            'vehicle_id' => $vehicle->id,
            'status' => TransportStatusEnum::IN_TRANSIT->value,
            'assignment_status' => TransportAssignmentStatusEnum::ASSIGNED->value,
        ]);

        Transport::factory()->count(2)->create([
            'driver_id' => $driver->id,
            'vehicle_id' => $vehicle->id,
            'status' => TransportStatusEnum::DELIVERED->value,
            'assignment_status' => TransportAssignmentStatusEnum::ASSIGNED->value,
        ]);
        Passport::actingAs($user);
        $response = $this
            ->getJson('/api/v1/transports?status=in_transit');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'status',
                    ],
                ],
                'meta' => [
                    'current_page',
                    'per_page',
                    'total',
                ],
            ])
            ->assertJsonCount(3, 'data')
            ->assertJsonPath('meta.total', 3);
    }

    public function test_it_filters_transports_by_status()
    {
        $user = PublicUser::factory()->create();
        $driver = Driver::factory()->create(['user_id' => $user->id]);
        $vehicle = Vehicle::factory()->create(['driver_id' => $driver->id]);

        Transport::factory()->count(2)->create([
            'driver_id' => $driver->id,
            'vehicle_id' => $vehicle->id,
            'status' => TransportStatusEnum::IN_TRANSIT->value,
            'assignment_status' => TransportAssignmentStatusEnum::ASSIGNED->value,
        ]);

        Transport::factory()->count(3)->create([
            'driver_id' => $driver->id,
            'vehicle_id' => $vehicle->id,
            'status' => TransportStatusEnum::DELIVERED->value,
            'assignment_status' => TransportAssignmentStatusEnum::ASSIGNED->value,
        ]);
        Passport::actingAs($user);
        $response = $this
            ->getJson('/api/v1/transports?status=delivered');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonPath('meta.total', 3);
    }

    public function test_it_paginates_results()
    {
        $user = PublicUser::factory()->create();
        $driver = Driver::factory()->create(['user_id' => $user->id]);
        $vehicle = Vehicle::factory()->create(['driver_id' => $driver->id]);

        Transport::factory()->count(15)->create([
            'driver_id' => $driver->id,
            'vehicle_id' => $vehicle->id,
            'status' => TransportStatusEnum::DELIVERED->value,
            'assignment_status' => TransportAssignmentStatusEnum::ASSIGNED->value,
        ]);
        Passport::actingAs($user);
        $response = $this
            ->getJson('/api/v1/transports?status=delivered');

        $response->assertStatus(200)
            ->assertJsonCount(15, 'data')
            ->assertJsonPath('meta.per_page', 15)
            ->assertJsonPath('meta.total', 15);

    }

    public function test_it_returns_unauthorized_for_non_driver_users()
    {
        $user = PublicUser::factory()->create(); // No driver associated
        Passport::actingAs($user);
        $response = $this
            ->getJson('/api/v1/transports');

        $response->assertStatus(403); // Forbidden
    }
}
