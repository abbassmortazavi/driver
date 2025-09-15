<?php

namespace App\Http\Requests\TransportTrackingLog;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    required: ['lat', 'lng', 'recorded_at'],
    properties: [
        new OA\Property(property: 'lat', type: 'string', nullable: false),
        new OA\Property(property: 'lng', type: 'string', nullable: false),
        new OA\Property(property: 'recorded_at', type: 'string', nullable: false),
    ]
)]
class CreateTransportLogLocationRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'locations' => 'required|array|min:1',
            'locations.*.lat' => [
                'required',
                'string',
                'regex:/^-?\d{1,3}(\.\d{1,15})?$/',
                function ($attribute, $value, $fail) {
                    $floatValue = (float) $value;
                    if ($floatValue < -90 || $floatValue > 90) {
                        $fail('The '.$attribute.' must be between -90 and 90 degrees.');
                    }
                },
            ],
            'locations.*.lng' => [
                'required',
                'string',
                'regex:/^-?\d{1,3}(\.\d{1,15})?$/',
                function ($attribute, $value, $fail) {
                    $floatValue = (float) $value;
                    if ($floatValue < -180 || $floatValue > 180) {
                        $fail('The '.$attribute.' must be between -180 and 180 degrees.');
                    }
                },
            ],
            'locations.*.recorded_at' => [
                'required',
                'date_format:Y-m-d H:i:s',
                'before_or_equal:now',
            ],
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'locations.required' => 'At least one location is required',
            'locations.*.lat.required' => 'Latitude is required for each location',
            'locations.*.lng.required' => 'Longitude is required for each location',
            'locations.*.recorded_at.required' => 'Timestamp is required for each location',
            'locations.*.recorded_at.date_format' => 'Timestamp must be in format Y-m-d H:i:s',
            'locations.*.recorded_at.before_or_equal' => 'Timestamp cannot be in the future',
        ];
    }
}
