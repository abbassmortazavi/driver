<?php

namespace App\Http\Requests\Vehicle;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use OpenApi\Attributes as OA;
use Patoughi\Common\Enums\VehicleStatusEnum;

#[OA\Schema(
    required: [],
    properties: [
        new OA\Property(property: 'status', type: 'string', enum: VehicleStatusEnum::class),
    ]
)]
class VehicleStatusUpdateRequest extends FormRequest
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
            'status' => ['required', new Enum(VehicleStatusEnum::class)],
        ];
    }
}
