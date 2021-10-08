<?php

return [

    /*
     * SSO Server full URL (http/https://website.com)
     */
    'server' => env('SSO_SERVER_URL'),

    /*
     * Requests made over https or http
     */
    'secure' => false,

    /*
     * SSO Server credentials
     */
    'client_id' => env('SSO_CLIENT_ID'),
    'client_secret' => env('SSO_CLIENT_SECRET'),

    /*
     * Laravel Route names in this application where to redirect users after login/register or logout
     */
    'redirect_authenticated_to_route' => 'home',
    'redirect_unauthenticated_to_route' => 'home',

    /*
     * Force user to verify email or phone
     */
    'user_must_verify_email' => true,
    'user_must_verify_phone' => true,

    /*
     * Re-fetch data from server if older than X minutes
     */
    'refresh_user_data_after_minutes' => 10,

    /*
     * User class namespace in this application
     */
    'user_class' => '\App\Models\User',

    /*
     * Add fields you want to access directly ($user->companyname)
     * Instead of using the 'sso_data' attribute ($user->sso_data->companyname)
     */
    'injected_sso_fields' => [
        // companyname
        // address
    ],

    /*
     * Set true to hide 'sso_data' attribute from serialization
     * same as writing ## $hidden = ['sso_data'] ## into your Model
     */
    'exclude_sso_data_from_serialization' => false

];