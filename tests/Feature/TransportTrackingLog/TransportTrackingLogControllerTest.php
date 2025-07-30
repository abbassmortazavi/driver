<?php

namespace Tests\Feature\TransportTrackingLog;

use App\Models\Driver;
use App\Models\PublicUser;
use App\Models\Transport;
use App\Models\TransportTrackingLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Patoughi\Common\Enums\TransportAssignmentStatusEnum;
use Patoughi\Common\Enums\TransportStatusEnum;
use Tests\TestCase;
use Vinkla\Hashids\Facades\Hashids;

class TransportTrackingLogControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function test_it_stores_locations_with_duplicate_checking()
    {
        // Arrange
        $user = PublicUser::factory()->create();
        $driver = Driver::factory()->create(['user_id' => $user->id]);
        $transport = Transport::factory()->create([
            'driver_id' => $driver->id,
            'status' => TransportStatusEnum::IN_TRANSIT->value,
            'assignment_status' => TransportAssignmentStatusEnum::ASSIGNED->value,
        ]);

        // Create an existing log for duplicate testing
        TransportTrackingLog::factory()->create([
            'transport_id' => $transport->id,
            'recorded_at' => '2025-07-28 07:00:33',
        ]);

        $locations = [
            [
                'lat' => '10.287896',
                'lng' => '25.388481',
                'recorded_at' => '2025-07-28 07:00:33', // This should be skipped
            ],
            [
                'lat' => '10.287897',
                'lng' => '25.388482',
                'recorded_at' => '2025-07-28 07:00:34', // This should be inserted
            ],
        ];

        // Act
        Passport::actingAs($user);
        $hashed = Hashids::encode($transport->id);
        $response = $this->postJson("/api/v1/transports/{$hashed}/locations", [
            'locations' => $locations,
        ]);

        // Assert
        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['lat', 'lng', 'driver', 'transport', 'recorded_at'],
                ],
                'meta' => [
                    'summary' => ['total_received', 'inserted', 'skipped'],
                ],
                'success',
            ])
            ->assertJsonCount(1, 'data') // Only one new record inserted
            ->assertJsonFragment([
                'lat' => '10.287897',
                'lng' => '25.388482',
            ])
            ->assertJsonMissing([
                'lat' => '10.287896',
                'lng' => '25.388481',
            ])
            ->assertJson([
                'meta' => [
                    'summary' => [
                        'total_received' => 2,
                        'inserted' => 1,
                        'skipped' => 1,
                    ],
                ],
                'success' => true,
            ]);

        $this->assertDatabaseCount('transport_tracking_logs', 2); // Original + 1 new
    }

    /**
     * @return void
     */
    public function test_it_denies_access_for_non_driver_users()
    {
        $user = PublicUser::factory()->create(); // User without driver
        $transport = Transport::factory()->create();

        Passport::actingAs($user);
        $hashed = Hashids::encode($transport->id);
        $response = $this->postJson("/api/v1/transports/{$hashed}/locations", [
            'locations' => [
                [
                    'lat' => '10.287896',
                    'lng' => '25.388481',
                    'recorded_at' => '2025-07-28 07:00:33',
                ],
            ],
        ]);

        $response->assertForbidden();
    }

    /**
     * @return void
     */
    public function test_it_requires_authentication()
    {
        $transport = Transport::factory()->create();
        $hashed = Hashids::encode($transport->id);
        $response = $this->postJson("/api/v1/transports/{$hashed}/locations", [
            'locations' => [],
        ]);

        $response->assertUnauthorized();
    }

    /**
     * @return void
     */
    public function test_it_validates_request_structure()
    {
        $user = PublicUser::factory()->create();
        $driver = Driver::factory()->create(['user_id' => $user->id]);
        $transport = Transport::factory()->create(['driver_id' => $driver->id]);

        Passport::actingAs($user);
        $hashed = Hashids::encode($transport->id);
        $response = $this->postJson("/api/v1/transports/{$hashed}/locations", [
            'locations' => [
                [
                    'lat' => 'invalid', // Invalid latitude
                    'lng' => '25.388481',
                    'recorded_at' => 'not-a-date', // Invalid date
                ],
            ],
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors([
                'locations.0.lat',
                'locations.0.recorded_at',
            ]);
    }

    /**
     * @return void
     */
    public function test_it_handles_empty_locations_array()
    {
        $user = PublicUser::factory()->create();
        Driver::factory()->create(['user_id' => $user->id]);
        $transport = Transport::factory()->create();

        Passport::actingAs($user);
        $hashed = Hashids::encode($transport->id);
        $response = $this->postJson("/api/v1/transports/{$hashed}/locations", [
            'locations' => [],
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['locations']);
    }
}
