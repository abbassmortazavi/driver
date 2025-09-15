<?php

namespace Tests\Feature\Station;

use App\Models\Station;
use Tests\TestCase;

class LoadingLocationListTest extends TestCase
{
    public function test_loading_locations_list_returns_all_locations_for_station()
    {
        $station = Station::factory()
            ->hasLoadingLocations(3)
            ->create();

        $response = $this->getJson("/api/v1/stations/{$station->getHashedId()}/loading-locations");

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'mdm_organization_id',
                    'status',
                    'name',
                    'description',
                    'station_id',
                    'usage',
                    'driver_instruction',
                ],
            ],
        ]);
    }
}
