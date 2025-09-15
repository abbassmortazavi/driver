<?php

namespace App\Http\Controllers\Api\V1\TransportTrackingLog;

use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Requests\TransportTrackingLog\CreateTransportLogLocationRequest;
use App\Http\Resources\Api\V1\TransportTrackingLog\TransportTrackingLogResource;
use App\Http\Responses\Api\V1\ApiResponse;
use App\Models\Transport;
use App\Models\TransportTrackingLog;
use App\Repository\TransportTrackingLog\TransportTrackingLogRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

#[OA\Post(
    path: '/api/v1/transports/{transport}/locations',
    operationId: 'location',
    description: 'Accepts an array of location objects and stores them with duplicate checking based on timestamp',
    summary: 'Store Location with duplicate checking',
    security: [['bearerAuth' => []]],
    tags: ['TransportTrackingLog'],
    parameters: [
        new OA\Parameter(
            name: 'transport',
            description: 'Transport ID',
            in: 'path',
            required: true,
            schema: new OA\Schema(type: 'string')
        ),
    ]
)]
#[OA\RequestBody(
    description: 'Array of location objects to store',
    required: true,
    content: new OA\JsonContent(
        properties: [
            new OA\Property(
                property: 'locations',
                type: 'array',
                items: new OA\Items(
                    required: ['lat', 'lng', 'recorded_at'],
                    properties: [
                        new OA\Property(
                            property: 'lat',
                            description: 'Latitude coordinate',
                            type: 'string',
                            pattern: '/^-?\d{1,3}(\.\d{1,15})?$/',
                            example: '10.287896'
                        ),
                        new OA\Property(
                            property: 'lng',
                            description: 'Longitude coordinate',
                            type: 'string',
                            pattern: '/^-?\d{1,3}(\.\d{1,15})?$/',
                            example: '25.388481'
                        ),
                        new OA\Property(
                            property: 'recorded_at',
                            description: 'Timestamp when location was recorded (format: Y-m-d H:i:s)',
                            type: 'string',
                            format: 'date-time',
                            example: '2025-07-28 07:00:33'
                        ),
                    ],
                    type: 'object'
                ),
                minItems: 1
            ),
        ],
        type: 'object'
    )
)]
#[OA\Response(
    response: Response::HTTP_OK,
    description: 'Location tracking result',
    content: new OA\JsonContent(
        properties: [
            new OA\Property(
                property: 'data',
                description: 'Array of successfully inserted locations',
                type: 'array',
                items: new OA\Items(
                    properties: [
                        new OA\Property(
                            property: 'lat',
                            type: 'string',
                            example: '10.287896'
                        ),
                        new OA\Property(
                            property: 'lng',
                            type: 'string',
                            example: '25.388481'
                        ),
                        new OA\Property(
                            property: 'driver',
                            type: 'string',
                            example: 'Luigi Dach'
                        ),
                        new OA\Property(
                            property: 'transport',
                            type: 'string',
                            example: 'Lilla Bauch III'
                        ),
                        new OA\Property(
                            property: 'recorded_at',
                            type: 'string',
                            format: 'date-time',
                            example: '2025-07-28 07:03:33'
                        ),
                    ],
                    type: 'object'
                )
            ),
            new OA\Property(
                property: 'meta',
                properties: [
                    new OA\Property(
                        property: 'summary',
                        properties: [
                            new OA\Property(
                                property: 'total_received',
                                type: 'integer',
                                example: 2
                            ),
                            new OA\Property(
                                property: 'inserted',
                                type: 'integer',
                                example: 1
                            ),
                            new OA\Property(
                                property: 'skipped',
                                type: 'integer',
                                example: 1
                            ),
                        ],
                        type: 'object'
                    ),
                ],
                type: 'object'
            ),
            new OA\Property(
                property: 'success',
                type: 'boolean',
                example: true
            ),
        ],
        type: 'object'
    )
)]
#[OA\Response(
    response: Response::HTTP_UNPROCESSABLE_ENTITY,
    description: 'Validation error',
    content: new OA\JsonContent(
        ref: '#/components/schemas/ApiResponseErrorValidation',
    )
)]
#[OA\Response(
    response: Response::HTTP_UNAUTHORIZED,
    description: 'Unauthorized',
    content: new OA\JsonContent(
        ref: '#/components/schemas/ApiResponseErrorUnauthorized',
    )
)]
class TransportTrackingLogSubmitLocationController extends ApiController
{
    /**
     * @param  TransportTrackingLogRepositoryInterface  $transportTrackingLogRepository
     */
    public function __construct(protected TransportTrackingLogRepositoryInterface $transportTrackingLogRepository) {}

    /**
     * @param  CreateTransportLogLocationRequest  $request
     * @param  Transport  $transport
     * @return JsonResponse
     */
    public function __invoke(CreateTransportLogLocationRequest $request, Transport $transport): JsonResponse
    {
        Gate::authorize('check-transport-status', $transport);
        $driverId = auth()->user()->driver->getKey();
        $transportId = $transport->getKey();

        $results = collect($request->locations)->map(function ($location) use ($transportId, $driverId) {
            $exists = TransportTrackingLog::query()->where([
                'transport_id' => $transportId,
                'recorded_at' => $location['recorded_at'],
            ])->exists();

            if ($exists) {
                return ['status' => 'skipped', 'location' => $location];
            }

            // Create new record
            $log = $this->transportTrackingLogRepository->create([
                'lat' => $location['lat'],
                'lng' => $location['lng'],
                'recorded_at' => $location['recorded_at'],
                'driver_id' => $driverId,
                'transport_id' => $transportId,
            ]);

            return ['status' => 'inserted', 'log' => $log];
        });

        // Separate inserted and skipped records
        $insertedLogs = $results->where('status', 'inserted')->pluck('log');
        $skippedCount = $results->where('status', 'skipped')->count();

        return ApiResponse::ok(
            data: TransportTrackingLogResource::collection($insertedLogs),
            meta: [
                'summary' => [
                    'total_received' => count($request->locations),
                    'inserted' => $insertedLogs->count(),
                    'skipped' => $skippedCount,
                ],
            ]
        );

    }
}
