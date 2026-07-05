<?php

return [
    'default' => env('SMS_DRIVER', 'vonage'),

    'connections' => [
        'vonage' => [
            'vonage_from' => '',
            'api_key' => '',
            'api_secret' => '',
        ],

        'twilio' => [
            'twilio_sid' => '',
            'twilio_auth_token' => '',
            'twilio_phone' => '',
        ],
    ],
];
