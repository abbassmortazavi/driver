<?php

namespace Tests\Feature\Ticket;

use App\Models\Driver;
use App\Models\PublicUser;
use App\Models\Ticket;
use App\Models\TicketCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class TicketReplyTest extends TestCase
{
    use RefreshDatabase;

    public function test_driver_can_reply_to_ticket()
    {
        $user = PublicUser::factory()->create();

        $driver = Driver::factory()->create(['user_id' => $user->id]);

        Passport::actingAs($user);

        $category = TicketCategory::factory()->create();

        // Create a ticket for the user
        $ticket = Ticket::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'subject' => 'Test Ticket',
            'description' => 'This is a test ticket.',
            'priority' => 'low',
        ]);
        $payload = [
            'message' => 'This is a reply to the ticket.',
        ];
        // dd($user->driver);
        $response = $this->postJson("/api/v1/tickets/{$ticket->getHashedId()}/reply", $payload);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'message',
                    'sender_id',
                    'created_at',
                ],
            ]);
    }

    public function test_non_owner_cannot_reply_to_ticket()
    {
        // Create the ticket owner and their driver
        $owner = PublicUser::factory()->create();
        $ownerDriver = \App\Models\Driver::factory()->create(['user_id' => $owner->id]);

        // Create another user (not the owner) and their driver
        $otherUser = PublicUser::factory()->create();
        $otherDriver = \App\Models\Driver::factory()->create(['user_id' => $otherUser->id]);
        Passport::actingAs($otherUser);

        // Create a ticket category
        $category = TicketCategory::factory()->create();

        // Create a ticket for the owner
        $ticket = Ticket::factory()->create([
            'user_id' => $owner->id,
            'category_id' => $category->id,
            'subject' => 'Test Ticket',
            'description' => 'This is a test ticket.',
            'priority' => 'low',
        ]);

        $payload = [
            'message' => 'This is a reply to the ticket.',
        ];

        $response = $this->postJson("/api/v1/tickets/{$ticket->getHashedId()}/reply", $payload);

        $response->assertStatus(403); // Forbidden
    }
}
