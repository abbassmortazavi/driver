<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    required: ['phone_number'],
    properties: [
        new OA\Property(property: 'phone_number', type: 'string'),
    ]
)]
class SendOtpRequest extends FormRequest
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
            // 'type' => 'required|in:email,sms',
            // 'email' => 'required_if:type,email|email|prohibited_if:type,sms',
            // 'phone_number' => 'required_if:type,sms|prohibited_if:type,email'

            'phone_number' => [
                'required',
                'string',
                'regex:/^(09\d{9}|\+\d{1,3}\d{4,14})$/',
            ],
        ];
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'phone.regex' => 'The phone number must be in valid format (09xxxxxxxx or international format)',
        ];
    }
}
