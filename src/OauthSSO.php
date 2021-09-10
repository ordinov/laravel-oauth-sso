<?php

namespace Ordinov\OauthSSO;
use Ordinov\OauthSSO\ConfiguredClass;
use Ordinov\OauthSSO\OauthSSOClient;
use Ordinov\OauthSSO\OauthSSORedirect;
use Ordinov\OauthSSO\OauthSSOProvider;
class OauthSSO extends ConfiguredClass
{
    public $client, 
           $redirect,
           $provider;

    public function __construct() {
        $this->client = new OauthSSOClient();
        $this->provider = new OauthSSOProvider($this->client);
        $this->redirect = new OauthSSORedirect();
    }

}