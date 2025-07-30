<?php

namespace App\Http\Requests\Ticket;

use App\Http\Requests\Traits\HashidDecodable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Patoughi\Common\Enums\TicketPriorityEnum;
use Patoughi\Common\Enums\TicketStatusEnum;
use OpenApi\Attributes as OA;

#[OA\Schema(
    required: ['category_id', 'subject', 'description', 'status'],
    properties: [
        new OA\Property(property: 'category_id', type: 'string', example: 'example'),
        new OA\Property(property: 'subject', type: 'string', example: 'example'),
        new OA\Property(property: 'description', type: 'string', example: 'example'),
        new OA\Property(property: 'priority', type: 'string', enum: TicketPriorityEnum::class, example: 'low'),
        new OA\Property(property: 'status', type: 'string', enum: TicketStatusEnum::class, example: 'open', nullable: true),
    ]
)]

class TicketSubmitRequest extends FormRequest
{
    use HashidDecodable;

    /**
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $this->decodeHashidFields([
            'category_id',
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
            'category_id' => ['required', 'exists:ticket_categories,id'],
            'subject' => ['required', 'string'],
            'description' => ['required', 'string'],
            'priority' => ['nullable', Rule::enum(TicketPriorityEnum::class)],
            'status' => ['nullable', Rule::enum(TicketStatusEnum::class)],
        ];
    }
}
