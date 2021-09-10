<?php

namespace Ordinov\OauthSSO;
use Ordinov\OauthSSO\ConfiguredClass;
use \GuzzleHttp\Client;

class OauthSSOClient extends ConfiguredClass
{
    public function __construct() {
        $this->token = session()->get('access_token');
        $this->guzzleConstructor = [
            'base_uri' => $this->config('server'),
            'verify' => $this->config('secure'),
            'http_errors' => false
        ];
    }

    public function getToken() {
        return session()->get('access_token');
    }

    public function getAuthHeaders() {
        return [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $this->getToken()
        ];
    }

    public function get($url, $params = [], $withAuthToken = true) {
        $guzzleClient = new Client($this->guzzleConstructor);
        $response = $guzzleClient->get($url, [
            'headers' => $withAuthToken ? $this->getAuthHeaders() : [],
            'query' => $params
        ]);
        $responseData = json_decode($response->getBody()->getContents());
        // not ok
        if (substr((string)$response->getStatusCode(),0,1) !== "2") {
            if (isset($responseData->message) && $responseData->message === 'Unauthenticated.') {
                auth()->logout();
                Session()->flush();
            }
            throw new \Exception($responseData->message);
        }
        return $responseData;
    }

    public function post($url, $params = [], $withAuthToken = true) {
        $guzzleClient = new Client($this->guzzleConstructor);
        $response = $guzzleClient->post($url, [
            'headers' => $withAuthToken ? $this->getAuthHeaders() : [],
            'form_params' => $params
        ]);
        $responseData = json_decode($response->getBody()->getContents());
        // not ok
        if (substr((string)$response->getStatusCode(),0,1) !== "2") {
            if (isset($responseData->message) && $responseData->message === 'Unauthenticated.') {
                auth()->logout();
                Session()->flush();
            }
        }
        return $responseData;
    }

}