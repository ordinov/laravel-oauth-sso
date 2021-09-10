<?php

if (!function_exists('user')) {
    function user() {
        if (!auth()->check()) { return false; }
        return auth()->user();
    }
}

function sso_sync_user_data($user, $userData, $syncToDB = true) 
{
    $userData->synced_on = now();
    Session::forget('ssoAuthData');
    Session::put('ssoAuthData', $userData);
    if ($syncToDB) {
        $fields = $user->getFillable();
        foreach ($fields as $field) {
            if ($field === 'sso_id' || isset($userData->{$field})) {
                $user->fill([$field => ($field === 'sso_id' ? $userData->id : $userData->{$field}) ]);
            }
        }
        $user->save();
    }
}