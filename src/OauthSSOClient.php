<?php

namespace Ordinov\OauthSSO;
use Ordinov\OauthSSO\ConfiguredClass;
use \GuzzleHttp\Client;

class OauthSSOClient extends ConfiguredClass
{
    public function __construct() {
        $this->token = session()->get('access_token');
        $this->guzzleConstructor = [
            'base_uri' => $this->config('server_private_url'),
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

    public function getUnauthHeaders() {
        return [
            'Accept' => 'application/json'
        ];
    }

    public function get($url, $params = [], $withAuthToken = true) {
        $guzzleClient = new Client($this->guzzleConstructor);
        $response = $guzzleClient->get($url, [
            'headers' => $withAuthToken ? $this->getAuthHeaders() : $this->getUnauthHeaders(),
            'query' => $params
        ]);
        $responseData = json_decode($response->getBody()->getContents());
        // not ok
        if (substr((string)$response->getStatusCode(),0,1) !== "2") {
            if (isset($responseData->message) && $responseData->message === 'Unauthenticated.') {
                auth()->logout();
                Session()->flush();
                throw new \Illuminate\Auth\AuthenticationException($responseData->message);
            }
            throw new \Exception($responseData->message);
        }
        return $responseData;
    }

    public function post($url, $params = [], $withAuthToken = true) {
        $guzzleClient = new Client($this->guzzleConstructor);
        $response = $guzzleClient->post($url, [
            'headers' => $withAuthToken ? $this->getAuthHeaders() : $this->getUnauthHeaders(),
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