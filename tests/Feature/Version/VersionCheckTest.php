<?php

namespace Tests\Feature\Version;

use App\Models\Version;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Patoughi\Common\Enums\VersionStatusEnum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class VersionCheckTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function test_it_returns_404_when_no_version_found()
    {
        DB::table('versions')->truncate();
        $response = $this->getJson('/api/v1/versions/check?platform=android&version=1.1.0&type=driver');

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(['message' => trans('messages.version_is_not_found')]);
    }

    /**
     * @return void
     */
    public function test_it_returns_error_when_client_version_is_higher_than_latest()
    {
        //refresh vehicles data
        DB::table('versions')->truncate();
        Version::factory()->create([
            'version' => '1.0.0',
            'platform' => 'android',
            'type' => 'driver',
            'status' => VersionStatusEnum::RELEASED->value,
        ]);

        $response = $this->getJson('/api/v1/versions/check?platform=android&version=2.0.0&type=driver');

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(['message' => trans('messages.your_version_is_not_Valid')]);
    }

    /**
     * @return void
     */
    public function test_it_requires_version_platform_and_type_parameters()
    {
        $response = $this->getJson('/api/v1/versions/check');

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_it_shows_force_update_when_update_is_required()
    {
        //refresh vehicles data
        DB::table('versions')->delete();
        Version::factory()->create([
            'version' => '2.0.0',
            'platform' => 'android',
            'force_update' => true,
            'type' => 'driver',
            'status' => VersionStatusEnum::RELEASED->value,
        ]);
        $response = $this->getJson('/api/v1/versions/check?platform=android&version=1.0.0&type=driver');

        $response->assertOk()
            ->assertJsonPath('data.force_update', true);
    }

}
