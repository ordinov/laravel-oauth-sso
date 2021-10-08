<?php

if (!function_exists('user')) {
    function user() {
        if (!auth()->check()) { return false; }
        return auth()->user();
    }
}

function sso_get_collection_cached_data() {
    return cache()->has('ssoCollectionData') ? cache()->get('ssoCollectionData') : [];
}

function sso_user_expired($userData) {
    return $userData->synced_on->lt(
        now()->subMinutes(config('sso.refresh_user_data_after_minutes'))
    );
}

function sso_cache_user($user, $userData) {
    $cache = sso_get_collection_cached_data();
    $cache[(int)$user->sso_id] = $userData;
    cache()->put('ssoCollectionData', $cache);
    sso_sync_user_data($user, $userData);
    return $userData;
}

function sso_cache_users($users) {
    cache()->put('ssoCollectionData', $users);
}

function sso_sync_user_data($user, $userData, $syncToDB = true) 
{
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