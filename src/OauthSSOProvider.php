<?php

namespace Ordinov\OauthSSO;
use Ordinov\OauthSSO\ConfiguredClass;
use Ordinov\OauthSSO\OauthSSOClient;

class OauthSSOProvider extends ConfiguredClass
{
    public $client;
    
    public function __construct(OauthSSOClient $client) {
        $this->client = $client;
    }

    public function getCachedUser($user) {
        $data = sso_get_collection_cached_data();
        if (isset($data[(int)$user->sso_id])) {
            if (!sso_user_expired($data[(int)$user->sso_id])) {
                return $data[(int)$user->sso_id];
            }
        }
        return sso_cache_user($user, $this->getUser($user->sso_id));
    }

    public function getUser($sso_id = null) {
        $user = $sso_id 
            ? $this->client->get("api/user/$sso_id", [], true)
            : $this->client->get("api/user", [], true);
        $user->synced_on = now();
        return $user;
    }

    public function doCacheUsers($users = []) {
        if (empty($users)) { return false; }
        $cache = sso_get_collection_cached_data();
        $missing_ids = [];
        foreach ($users as $user) {
            $sso_id = (int)$user->sso_id;
            if (!isset($cache[$sso_id]) || sso_user_expired($cache[$sso_id])) {
                $missing_ids[] = $sso_id;
            }
        }

        $remoteUsers = $this->getUsers($missing_ids);
        $allUsers = $cache + $remoteUsers;

        sso_cache_users($allUsers);
    }

    public function getUsers($sso_ids = []) {
        $synced_on = now();
        $users = $this->client->get("api/users", ['ids' => $sso_ids], true);
        $return = [];
        foreach ($users as $k => $v) {
            $v->synced_on = $synced_on;
            $return[(int)$v->id] = $v;
        }
        return $return;
    }
}