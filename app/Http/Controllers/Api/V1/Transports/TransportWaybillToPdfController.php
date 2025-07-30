<?php

namespace App\Http\Controllers\Api\V1\Transports;

use App\Http\Controllers\Api\V1\ApiController;
use App\Models\Transport;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Gate;
use Mpdf\Mpdf;
use Mpdf\MpdfException;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

#[OA\Get(
    path: '/api/v1/transports/{transport}/waybill-pdf',
    operationId: 'getTransportWaybillPDF',
    description: 'Generates a PDF document containing all waybills associated with the specified transport',
    summary: 'Generate Waybill PDF for Transport',
    security: [['bearerAuth' => []]],
    tags: ['Transport']
)]
#[OA\Parameter(
    name: 'transport',
    description: 'Transport ID',
    in: 'path',
    required: true,
)]
#[OA\Response(
    response: Response::HTTP_OK,
    description: 'PDF document generated successfully',
    headers: [
        new OA\Header(
            header: 'Content-Disposition',
            description: 'Attachment with filename',
            schema: new OA\Schema(type: 'string')
        ),
    ],
    content: new OA\MediaType(
        mediaType: 'application/pdf',
        schema: new OA\Schema(type: 'string', format: 'binary')
    )
)]
#[OA\Response(
    response: Response::HTTP_FORBIDDEN,
    description: 'Unauthorized access',
    content: new OA\JsonContent(
        properties: [
            new OA\Property(property: 'success', type: 'boolean', example: false),
            new OA\Property(property: 'message', type: 'string', example: 'This action is unauthorized.'),
        ],
        type: 'object'
    )
)]
#[OA\Response(
    response: Response::HTTP_UNPROCESSABLE_ENTITY,
    description: 'Validation error',
    content: new OA\JsonContent(
        properties: [
            new OA\Property(property: 'success', type: 'boolean', example: false),
            new OA\Property(property: 'message', type: 'string', example: 'Validation failed'),
            new OA\Property(
                property: 'errors',
                type: 'object',
                example: ['field' => ['The field is required']],
                additionalProperties: new OA\AdditionalProperties(
                    type: 'array',
                    items: new OA\Items(type: 'string')
                )
            ),
        ],
        type: 'object'
    )
)]
#[OA\Response(
    response: Response::HTTP_INTERNAL_SERVER_ERROR,
    description: 'Server error during PDF generation',
    content: new OA\JsonContent(
        properties: [
            new OA\Property(property: 'success', type: 'boolean', example: false),
            new OA\Property(property: 'message', type: 'string', example: 'Server error occurred'),
        ],
        type: 'object'
    )
)]
class TransportWaybillToPdfController extends ApiController
{
    /**
     * @throws BindingResolutionException
     * @throws MpdfException
     * @throws Throwable
     */
    public function __invoke(Transport $transport)
    {
        Gate::authorize('view', $transport);
        $waybills = $transport->waybills;
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'direction' => 'rtl',
        ]);
        $mpdf->WriteHTML(view('waybills.pdf', compact('transport', 'waybills'))->render());

        return response()->make($mpdf->Output('waybills.pdf', 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="waybills.pdf"',
        ]);

    }
}
