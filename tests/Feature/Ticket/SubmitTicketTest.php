<?php

namespace Tests\Feature\Ticket;

use App\Models\PublicUser;
use App\Models\TicketCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Vinkla\Hashids\Facades\Hashids;

class SubmitTicketTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_submit_ticket()
    {
        // Create a user and authenticate
        $user = PublicUser::factory()->create();
        Passport::actingAs($user);
        // Create a ticket category
        $category = TicketCategory::factory()->create();

        $payload = [
            'category_id' => Hashids::encode($category->id),
            'subject' => 'Test Ticket',
            'description' => 'This is a test ticket.',
            'priority' => 'low',
        ];

        $response = $this->postJson('/api/v1/tickets', $payload);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'user_id',
                    'category',
                    'priority',
                    'subject',
                    'description',
                    'status',
                    'created_by',
                    'messages',
                ],
            ]);
    }
}
