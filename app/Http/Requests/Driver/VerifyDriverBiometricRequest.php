<?php

namespace App\Http\Requests\Driver;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    required: ['video'],
    properties: [
        new OA\Property(property: 'video', type: 'string', nullable: true),
    ]
)]
class VerifyDriverBiometricRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
//            'video' => 'required|file|mimetypes:video/mp4,video/quicktime|max:20000',
            'video' => 'required|string',
        ];
    }
}
