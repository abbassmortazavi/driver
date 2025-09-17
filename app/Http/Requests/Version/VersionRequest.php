<?php

namespace App\Http\Requests\Version;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;
use Patoughi\Common\Enums\VersionPlatformEnum;
use Patoughi\Common\Enums\VersionTypeEnum;

#[OA\Schema(
    required: ['platform', 'type', 'version'],
    properties: [
        new OA\Property(property: 'platform', type: 'string', enum: VersionPlatformEnum::class),
        new OA\Property(property: 'type', type: 'string', enum: VersionTypeEnum::class),
        new OA\Property(property: 'version', type: 'string'),
    ]
)]
class VersionRequest extends FormRequest
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
            'platform' => ['required', Rule::in(VersionPlatformEnum::cases())],
            'type' => ['required', Rule::in(VersionTypeEnum::cases())],
            'version' => 'required|string',
        ];
    }
}
