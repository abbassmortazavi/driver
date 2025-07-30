<?php

namespace App\Http\Requests\Bid;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    required: ['proposed_price', 'description'],
    properties: [
        new OA\Property(property: 'proposed_price', type: 'string', nullable: false),
        new OA\Property(property: 'description', type: 'string', nullable: true),
    ]
)]
class CreateBidRequest extends FormRequest
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
            'proposed_price' => 'required|numeric',
            'description' => 'nullable|string',
        ];
    }
}
