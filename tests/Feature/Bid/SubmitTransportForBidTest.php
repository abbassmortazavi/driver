<?php

namespace Tests\Feature\Bid;

use App\Models\Customer;
use App\Models\Driver;
use App\Models\Order;
use App\Models\PublicUser;
use App\Models\Shipment;
use App\Models\Transport;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Patoughi\Common\Enums\ShipmentStatusEnum;
use Patoughi\Common\Enums\TransportStatusEnum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Vinkla\Hashids\Facades\Hashids;

class SubmitTransportForBidTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_driver_can_submit_bid_for_transport()
    {
        $user = PublicUser::factory()->create();
        $customer = Customer::factory()->create([
            'user_id' => $user->id,
        ]);
        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'allow_price_negotiation' => true,
        ]);
        $driver = Driver::factory()->create(['user_id' => $user->id]);
        $vehicle = Vehicle::factory()->create();
        $shipment = Shipment::factory()->create([
            'order_id' => $order->id,
            'status' => fake()->randomElement(array_column(ShipmentStatusEnum::cases(), 'value')),
            'tracking_number' => $this->faker->randomFloat(5),
            'name' => $this->faker->name(),
            'total_weight' => $this->faker->randomFloat(2, 0, 1000),
            'total_volume' => $this->faker->randomFloat(2, 0, 1000),
        ]);

        $transport = Transport::factory()->create([
            'status' => fake()->randomElement(array_column(TransportStatusEnum::cases(), 'value')),
            'vehicle_id' => $vehicle->id,
            'expected_arrival_at' => $this->faker->dateTime(),
            'expected_departure_at' => $this->faker->dateTime(),
        ]);

        $transport->shipments()->attach($shipment);
        Passport::actingAs($user);

        $response = $this->postJson("/api/v1/transports/{$transport->getHashedId()}/bids", [
            'driver_id' => Hashids::encode($driver->id),
            'proposed_price_value' => 100.50,
            'description' => 'Test bid notes',
        ]);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'driver_id',
                    'transport_id',
                    'proposed_price',
                    'description',
                    'status',
                ],
            ]);

        $this->assertDatabaseHas('bids', [
            'driver_id' => $driver->id,
            'transport_id' => $transport->id,
        ]);
    }

    /**
     * @return void
     */
    public function test_cannot_submit_bid_for_nonexistent_transport()
    {
        $nonExistentTransportId = 9999;
        $user = PublicUser::factory()->create();
        $driver = Driver::factory()->create(['user_id' => $user->id]);
        Passport::actingAs($user);

        $response = $this->postJson("/api/v1/transports/{$nonExistentTransportId}/bids", [
            'driver_id' => Hashids::encode($driver->id),
            'proposed_price_value' => 100.50,
        ]);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * @return void
     */
    public function test_cannot_submit_bid_if_price_negotiation_not_allowed()
    {
        $user = PublicUser::factory()->create();
        $driver = Driver::factory()->create(['user_id' => $user->id]);
        Passport::actingAs($user);
        $order = Order::factory()->create(['allow_price_negotiation' => false]);
        $shipment = Shipment::factory()->create(['order_id' => $order->id]);
        $transport = Transport::factory()->create([
            'driver_id' => $driver->id,
        ]);

        $transport->shipments()->attach($shipment);

        $response = $this->postJson("/api/v1/transports/{$transport->getHashedId()}/bids", [
            'driver_id' => Hashids::encode($driver->id),
            'proposed_price_value' => 100.50,
        ]);

        $response->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertJson(['message' => trans('messages.you_can_not_do_this_bid')]);
    }
}
