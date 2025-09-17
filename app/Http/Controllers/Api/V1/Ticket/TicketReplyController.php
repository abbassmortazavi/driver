<?php

namespace App\Http\Controllers\Api\V1\Ticket;

use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Requests\Ticket\TicketReplyRequest;
use App\Http\Resources\Api\V1\Ticket\TicketMessageResource;
use App\Http\Responses\Api\V1\ApiResponse;
use App\Models\Ticket;
use App\Repository\TicketMessage\TicketMessageRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

#[OA\Post(
    path: '/api/v1/tickets/{ticket}/reply',
    operationId: 'replyTicket',
    summary: 'Reply ticket',
    security: [['bearerAuth' => []]],
    tags: ['Ticket'],
    parameters: [
        new OA\Parameter(
            name: 'ticket',
            description: 'The id of the ticket',
            in: 'path',
            required: true,
            schema: new OA\Schema(type: 'string')
        ),
    ]
)]
#[OA\RequestBody(
    description: 'Ticket Message Information',
    required: true,
    content: new OA\JsonContent(
        ref: \App\Http\Requests\Ticket\TicketReplyRequest::class
    )
)]
#[OA\Response(
    response: Response::HTTP_OK,
    description: 'Ticket Reply Submitted Successfully',
    content: new OA\JsonContent(
        properties: [
            new OA\Property(
                property: 'data',
                ref: \App\Http\Resources\Api\V1\Ticket\TicketMessageResource::class,
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
class TicketReplyController extends ApiController
{
    /**
     * @param  TicketMessageRepositoryInterface  $ticketMessageRepository
     */
    public function __construct(private TicketMessageRepositoryInterface $ticketMessageRepository) {}

    /**
     * @param  TicketReplyRequest  $request
     * @param  Ticket  $ticket
     * @return JsonResponse
     */
    public function __invoke(TicketReplyRequest $request, Ticket $ticket): JsonResponse
    {
        Gate::authorize('CheckOwner', $ticket);
        $ticketMessage = $this->ticketMessageRepository->create($this->prepareCreateData($request->validated(), $ticket));

        return ApiResponse::ok(TicketMessageResource::make($ticketMessage));
    }

    /**
     * @param  array  $payload
     * @param  Ticket  $ticket
     * @return array
     */
    private function prepareCreateData(array $payload, Ticket $ticket): array
    {
        return [
            'ticket_id' => $ticket->id,
            'sender_id' => auth()->user()->id,
            'message' => $payload['message'],
        ];
    }
}
