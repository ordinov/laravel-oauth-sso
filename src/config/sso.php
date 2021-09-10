<?php

return [

    'server' => env('SSO_SERVER_URL'),

    'secure' => false,

    'client_id' => env('SSO_CLIENT_ID'),
    'client_secret' => env('SSO_CLIENT_SECRET'),

    'redirect_authenticated_to_route' => 'home',
    'redirect_unauthenticated_to_route' => 'home',

    'user_must_verify_email' => true,
    'user_must_verify_phone' => true,

    'refresh_user_data_after_minutes' => 10,

    'user_class' => '\App\Models\User'

];