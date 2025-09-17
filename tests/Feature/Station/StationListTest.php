<?php

namespace Tests\Feature\Station;

use App\Models\Station;
use App\Repository\Station\StationRepositoryInterface;
use Mockery;
use Tests\TestCase;

class StationListTest extends TestCase
{
    public function test_station_list_returns_all_stations_successfully()
    {
        $stations = Station::factory()->count(3)->make();

        $mockRepo = Mockery::mock(StationRepositoryInterface::class);
        $mockRepo->shouldReceive('all')->once()->andReturn($stations);
        $this->app->instance(StationRepositoryInterface::class, $mockRepo);

        $response = $this->getJson(route('stations.list'));

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'mdm_organization_id',
                    'status',
                    'name',
                    'description',
                    'carrier_id',
                    'short_name',
                    'public_name',
                    'start_opening_time',
                    'end_opening_hour',
                    'injection_days',
                    'required_time_slot',
                    'address_country',
                    'address_city',
                    'address_state',
                    'address_street_and_house_number',
                    'address_postal_code',
                    'address_lat',
                    'address_lng',
                    'availability',
                    'stop_type',
                    'vehicle_restriction',
                ],
            ],
        ]);
    }
}
