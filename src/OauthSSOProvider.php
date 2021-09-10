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

    public function getUser($id = null) {
        return $id 
            ? $this->client->get("api/user/$id", [], true)
            : $this->client->get("api/user", [], true);
    }

    public function getUsers($ids = []) {
        return $this->client->get("api/users", compact('ids'), true);
    }
}