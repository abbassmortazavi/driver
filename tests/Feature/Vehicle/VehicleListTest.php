<?php

namespace Tests\Feature\Vehicle;

use App\Models\Driver;
use App\Models\PublicUser;
use App\Models\Vehicle;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\Passport;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class VehicleListTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function test_it_returns_list_of_vehicles_for_authenticated_driver()
    {
        $user = PublicUser::factory()->create();
        $driver = Driver::factory()->create(['user_id' => $user->id]);

        Vehicle::factory()->count(2)->create(['driver_id' => $driver->id]);

        Passport::actingAs($user);
        $response = $this->getJson('api/v1/vehicles?page=1&per_page=2');

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [],
            ])
            ->assertJsonCount(2, 'data');
    }

    /**
     * @throws BindingResolutionException
     */
    public function test_it_filters_vehicles_by_status_when_provided()
    {

        $user = PublicUser::factory()->create();
        $driver = Driver::factory()->create(['user_id' => $user->id]);

        // refresh vehicles data
        DB::table('vehicles')->delete();

        Vehicle::factory()->count(3)->create([
            'driver_id' => $driver->id,
            'status' => 'active',
        ]);
        Passport::actingAs($user);
        $response = $this->getJson('api/v1/vehicles?page=1&per_page=10&status=active');

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(3, 'data');
    }

    /**
     * @return void
     */
    public function test_it_access_denied_if_user_has_no_driver_profile()
    {
        $user = PublicUser::factory()->create();
        Vehicle::factory()->create();
        Passport::actingAs($user);

        $response = $this->getJson('api/v1/vehicles?page=1&per_page=2');

        $response->assertForbidden();
    }

    public function test_it_denies_access_for_unauthenticated_users()
    {
        $response = $this->getJson('api/v1/vehicles?page=1&per_page=10');
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
