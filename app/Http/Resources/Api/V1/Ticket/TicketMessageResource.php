<?php

namespace App\Http\Resources\Api\V1\Ticket;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    required: ['id', 'message', 'sender_id', 'created_at'],
    properties: [
        new OA\Property(property: 'id', type: 'string'),
        new OA\Property(property: 'message', type: 'string'),
        new OA\Property(property: 'sender_id', type: 'string'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
    ]
)]
class TicketMessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->getHashedId(),
            'message' => $this->message,
            'sender_id' => $this->sender->getHashedId(),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
