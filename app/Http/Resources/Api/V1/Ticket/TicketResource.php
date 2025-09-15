<?php

namespace App\Http\Resources\Api\V1\Ticket;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;
use Patoughi\Common\Enums\TicketCreatedByEnum;
use Patoughi\Common\Enums\TicketPriorityEnum;
use Patoughi\Common\Enums\TicketStatusEnum;

#[OA\Schema(
    required: ['id', 'user_id', 'category_id', 'priority', 'subject', 'description', 'status', 'created_by', 'messages'],
    properties: [
        new OA\Property(property: 'id', type: 'string'),
        new OA\Property(property: 'user_id', type: 'string'),
        new OA\Property(
            property: 'category',
            type: 'array',
            items: new OA\Items(ref: TicketCategoryResource::class)
        ),
        new OA\Property(property: 'priority', type: 'string', enum: TicketPriorityEnum::class),
        new OA\Property(property: 'subject', type: 'string'),
        new OA\Property(property: 'description', type: 'string'),
        new OA\Property(property: 'status', type: 'string', enum: TicketStatusEnum::class),
        new OA\Property(property: 'created_by', type: 'string', enum: TicketCreatedByEnum::class),
        new OA\Property(
            property: 'messages',
            type: 'array',
            items: new OA\Items(ref: TicketMessageResource::class)
        ),
    ]
)]
class TicketResource extends JsonResource
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
            'user_id' => $this->user->getHashedId(),
            'category' => TicketCategoryResource::make($this->category),
            'priority' => $this->priority,
            'subject' => $this->subject,
            'description' => $this->description,
            'status' => $this->status,
            'created_by' => $this->created_by,
            'messages' => TicketMessageResource::collection($this->whenLoaded('messages')),
        ];
    }
}
