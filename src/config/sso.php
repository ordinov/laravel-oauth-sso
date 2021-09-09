<?php

return [

    'server' => env('SSO_SERVER_URL'),

    'client_id' => env('SSO_CLIENT_ID'),
    'client_secret' => env('SSO_CLIENT_SECRET'),

    'redirect_authenticated_to_route' => 'home',
    'redirect_unauthenticated_to_route' => 'home'

];