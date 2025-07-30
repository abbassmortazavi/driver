<?php

namespace App\Http\Requests\Vehicle;

use App\Http\Requests\Traits\HashidDecodable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;
use Patoughi\Common\Enums\VehicleFuelTypeEnum;

#[OA\Schema(
    required: [
        'name',
        'vehicle_type_id',
        'fuel_type',
        'plate_number',
        'vin_number',
        'insurance_policy_number',
        'insurance_expiry_date',
        'color',
        'model_name',
        'brand_name',
        'year',
    ],
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
class CreateVehicleRequest extends FormRequest
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
            'name' => 'required|string',
            'vehicle_type_id' => 'required|exists:vehicle_types,id',
            'fuel_type' => ['required', Rule::in(VehicleFuelTypeEnum::getValues())],
            'plate_number' => 'required|string|max:20',
            'vin_number' => 'required|string|max:17',
            'insurance_policy_number' => 'required|string|max:50',
            'insurance_expiry_date' => 'required|string|date_format:Y-m-d',
            'color' => 'required|string|max:30',
            'model_name' => 'required|string|max:50',
            'brand_name' => 'required|string|max:50',
            'year' => 'required|string|max:4',
        ];
    }
}
