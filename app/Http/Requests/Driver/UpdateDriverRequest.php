<?php

namespace App\Http\Requests\Driver;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    required: ['licence_number', 'licence_type', 'licence_expired_at'],
    properties: [
        new OA\Property(property: 'licence_number', type: 'string'),
        new OA\Property(property: 'licence_type', type: 'string'),
        new OA\Property(property: 'licence_expired_at', type: 'string', format: 'date'),
        new OA\Property(property: 'emergency_contact_name', type: 'string', nullable: true),
        new OA\Property(property: 'emergency_contact_phone', type: 'string', nullable: true),
    ]
)]
class UpdateDriverRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'licence_number' => ['required', 'string', 'max:50'],
            'licence_type' => ['required', 'string', 'max:20'],
            'licence_expired_at' => ['required', 'date_format:Y-m-d'],
            'emergency_contact_name' => ['nullable', 'string', 'max:100'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:20'],
        ];
    }
}
