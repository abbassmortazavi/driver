<?php

namespace App\Http\Requests\Traits;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Vinkla\Hashids\Facades\Hashids;

trait HashidDecodable
{
    protected function decodeHashid($value)
    {
        if ($value) {
            $decoded = Hashids::decode($value);
        }

        return $decoded[0] ?? 0;
    }

    protected function decodeHashidFields(array $fields): void
    {
        $decodedFields = [];

        foreach ($fields as $field) {
            if (Str::contains($field, '*')) {
                $this->decodeWildcardField($field);
            } else {
                if ($this->has($field)) {
                    $decodedFields[$field] = $this->decodeHashid($this->input($field));
                }
            }
        }

        $this->merge($decodedFields);
    }

    protected function decodeWildcardField(string $wildcardKey): void
    {
        $pattern = str_replace(['.', '*'], ['\.', '[0-9]+'], $wildcardKey);
        $pattern = "/^$pattern$/";

        $input = $this->all();
        $flat = Arr::dot($input);

        foreach ($flat as $key => $value) {
            if (preg_match($pattern, $key)) {
                Arr::set($input, $key, $this->decodeHashid($value));
            }
        }

        $this->replace($input);
    }
}
