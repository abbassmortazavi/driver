<?php

namespace App\Http\Requests\Vehicle;

use App\Http\Requests\Traits\HashidDecodable;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;
use Illuminate\Foundation\Http\FormRequest;
use Patoughi\Common\Enums\VehicleFuelTypeEnum;

#[OA\Schema(
    schema: 'VehicleUpdateRequest',
    title: 'Vehicle Update Request',
    description: 'Request body for updating vehicle information',
    properties: [
        new OA\Property(property: 'name', type: 'string'),
        new OA\Property(property: 'vehicle_type_id', type: 'string'),
        new OA\Property(property: 'fuel_type', type: 'string', enum: VehicleFuelTypeEnum::class),
        new OA\Property(property: 'plate_number', type: 'string'),
        new OA\Property(property: 'vin_number', type: 'string'),
        new OA\Property(property: 'insurance_policy_number', type: 'string'),
        new OA\Property(property: 'insurance_expiry_date', type: 'string', format: 'date'),
        new OA\Property(property: 'color', type: 'string'),
        new OA\Property(property: 'model_name', type: 'string'),
        new OA\Property(property: 'brand_name', type: 'string'),
        new OA\Property(property: 'year', type: 'string'),
    ]
)]
class VehicleUpdateRequest extends FormRequest
{
    use HashidDecodable;

    /**
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $this->decodeHashidFields([
            'vehicle_type_id',
        ]);
    }

    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'nullable|string',
            'vehicle_type_id' => 'nullable|exists:vehicle_types,id',
            'fuel_type' => ['nullable', Rule::in(VehicleFuelTypeEnum::getValues())],
            'plate_number' => 'nullable|string|max:20',
            'vin_number' => 'nullable|string|max:17',
            'insurance_policy_number' => 'nullable|string|max:50',
            'insurance_expiry_date' => 'nullable|string|date_format:Y-m-d',
            'color' => 'nullable|string|max:30',
            'model_name' => 'nullable|string|max:50',
            'brand_name' => 'nullable|string|max:50',
            'year' => 'nullable|string|max:4',
        ];
    }
}
