<?php

namespace Tests\Feature\VehicleType;

use App\Models\VehicleType;
use App\Repository\VehicleType\VehicleTypeRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class VehicleTypeListTest extends TestCase
{
    use RefreshDatabase;

    public function test_vehicle_type_list_returns_all_vehicle_types_successfully()
    {
        $vehicleTypes = VehicleType::factory()->count(3)->make();

        $mockRepo = Mockery::mock(VehicleTypeRepositoryInterface::class);
        $mockRepo->shouldReceive('all')->once()->andReturn($vehicleTypes);
        $this->app->instance(VehicleTypeRepositoryInterface::class, $mockRepo);

        $response = $this->getJson(route('vehicle-types.list'));

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'name_en',
                    'vehicle_length',
                    'vehicle_width',
                    'capacity_pal',
                    'capacity_weight',
                    'max_loading_height',
                    'trip_type',
                    'rtmp_code',
                    'rtmp_min_capacity',
                    'rtmp_max_capacity',
                ],
            ],
        ]);
    }
}
