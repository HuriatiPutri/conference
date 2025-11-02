<?php

return [
    'client_id' => env('PAYPAL_SANDBOX_CLIENT_ID'),
    'client_secret' => env('PAYPAL_SANDBOX_CLIENT_SECRET'),
    'mode' => env('PAYPAL_MODE', 'sandbox'), // sandbox or live
    'sandbox_url' => 'https://api.sandbox.paypal.com',
    'live_url' => 'https://api.paypal.com',
    'currency' => 'USD',
];