<?php

return [
    'default' => env('SMS_PROVIDER', 'ghasedak'),
    'sender' => env('SMS_DEFAULT_SENDER'),

    'providers' => [
        'ghasedak' => [
            'driver' => 'ghasedak',
            'api_key' => env('GHASEDAK_API_KEY', '1921430375b44087d1d2a227dcfc80118a4425528736539bb3be7cb3b4db06d98tayuGyJmqiEespt'),
            'sender' => env('GHASEDAK_SENDER'),
            'timeout' => env('GHASEDAK_TIMEOUT', 10),
            'url' => env('GHASEDAK_URL', 'https://gateway.ghasedak.me/rest/api/v1/WebService'),
            'line_number' => env('GHASEDAK_LINE_NUMBER', '90002930'),
        ],
    ],
];
