<?php

namespace App\Http\Requests\Transport;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;
use Patoughi\Common\Enums\TransportAssignmentStatusEnum;
use Patoughi\Common\Enums\TransportStatusEnum;

#[OA\Schema(
    required: [],
    properties: [
        new OA\Property(property: 'page', type: 'string'),
        new OA\Property(property: 'per_page', type: 'string'),
        new OA\Property(property: 'status', type: 'string', enum: TransportStatusEnum::class),
        new OA\Property(property: 'assignment_status', type: 'string', enum: TransportAssignmentStatusEnum::class),
    ]
)]
class TransportToAssignedDriverListRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'page' => 'nullable|int',
            'per_page' => 'nullable|int',
            'status' => ['nullable', 'string',  Rule::in(TransportStatusEnum::getValues())],
            'assignment_status' => ['nullable', 'string',  Rule::in(TransportAssignmentStatusEnum::getValues())],
        ];
    }
}
