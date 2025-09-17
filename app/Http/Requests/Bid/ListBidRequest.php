<?php

namespace App\Http\Requests\Bid;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;
use Patoughi\Common\Enums\BidStatusEnum;

#[OA\Schema(
    required: ['status', 'per_page'],
    properties: [
        new OA\Property(property: 'status', type: 'string', enum: BidStatusEnum::class, nullable: true),
        new OA\Property(property: 'per_page', type: 'integer', default: 50, nullable: true),
    ]
)]
class ListBidRequest extends FormRequest
{
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
            'status' => ['nullable', Rule::in(BidStatusEnum::getValues())],
            'per_page' => 'nullable|integer|min:1',
        ];
    }
}
