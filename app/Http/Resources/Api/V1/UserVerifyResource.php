<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserVerifyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->getHashedId(),
            'name' => $this->name,
            'token' => [
                'access_token' => $this['token']['access_token'],
                'expires_in' => $this['token']['expires_in'],
                'refresh_token' => $this['token']['refresh_token'],
                'token_type' => $this['token']['token_type'],
            ],
        ];
    }
}
