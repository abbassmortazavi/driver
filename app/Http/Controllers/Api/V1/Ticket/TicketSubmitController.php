<?php

namespace App\Http\Controllers\Api\V1\Ticket;

use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Requests\Ticket\TicketSubmitRequest;
use App\Http\Resources\Api\V1\Ticket\TicketResource;
use App\Http\Responses\Api\V1\ApiResponse;
use App\Repository\Ticket\TicketRepositoryInterface;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;
use Patoughi\Common\Enums\TicketCreatedByEnum;
use Patoughi\Common\Enums\TicketPriorityEnum;
use Patoughi\Common\Enums\TicketStatusEnum;
use Symfony\Component\HttpFoundation\Response;

#[OA\Post(
    path: '/api/v1/tickets',
    operationId: 'submitTicket',
    summary: 'Submit new ticket',
    security: [['bearerAuth' => []]],
    tags: ['Ticket'],
)]
#[OA\RequestBody(
    description: 'Ticket Information',
    required: true,
    content: new OA\JsonContent(
        ref: \App\Http\Requests\Ticket\TicketSubmitRequest::class
    )
)]
#[OA\Response(
    response: Response::HTTP_OK,
    description: 'Ticket Submitted Successfully',
    content: new OA\JsonContent(
        properties: [
            new OA\Property(
                property: 'data',
                ref: \App\Http\Resources\Api\V1\Ticket\TicketResource::class,
                type: 'object'
            ),
        ]
    )
)]
#[OA\Response(
    response: Response::HTTP_UNPROCESSABLE_ENTITY,
    description: 'Validation error',
    content: new OA\JsonContent(
        ref: '#/components/schemas/ApiResponseErrorValidation'
    )
)]
#[OA\Response(
    response: Response::HTTP_INTERNAL_SERVER_ERROR,
    description: 'Server error',
    content: new OA\JsonContent(
        ref: '#/components/schemas/ApiResponseErrorServer'
    )
)]
class TicketSubmitController extends ApiController
{
    /**
     * @param  TicketRepositoryInterface  $ticketRepository
     */
    public function __construct(private TicketRepositoryInterface $ticketRepository) {}

    /**
     * @param  TicketSubmitRequest  $request
     * @return JsonResponse
     */
    public function __invoke(TicketSubmitRequest $request): JsonResponse
    {
        $ticket = $this->ticketRepository->create($this->prepareCreateData($request->validated()));
        $ticket->load('messages');

        return ApiResponse::ok(TicketResource::make($ticket));
    }

    /**
     * @param  array  $payload
     * @return array
     */
    private function prepareCreateData(array $payload): array
    {
        return [
            'user_id' => auth()->user()->id,
            'category_id' => $payload['category_id'],
            'priority' => $payload['priority'] ?? TicketPriorityEnum::LOW->value,
            'subject' => $payload['subject'],
            'description' => $payload['description'],
            'status' => TicketStatusEnum::OPEN->value,
            'created_by' => TicketCreatedByEnum::DRIVER->value,
        ];
    }
}
