<?php

namespace App\Http\Requests\Driver;

use App\Rules\IranianNationalCode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;
use Patoughi\Common\Enums\GenderEnum;

#[OA\Schema(
    required: ['first_name', 'last_name', 'national_code', 'gender', 'birth_date'],
    properties: [
        new OA\Property(property: 'first_name', type: 'string', nullable: false),
        new OA\Property(property: 'last_name', type: 'string', nullable: false),
        new OA\Property(property: 'national_code', type: 'string', nullable: false),
        new OA\Property(property: 'gender', type: 'string', enum: GenderEnum::class, nullable: false),
        new OA\Property(property: 'birth_date', type: 'string', format: 'date', nullable: false),
    ]
)]
class CreateDriverRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'national_code' => ['required', 'string', new IranianNationalCode()],
            'gender' => ['required', Rule::in(GenderEnum::getValues())],
            'birth_date' => ['required', 'date_format:Y-m-d'],
        ];
    }
}
