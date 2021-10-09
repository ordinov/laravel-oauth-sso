<?php

namespace Ordinov\OauthSSO;
class ConfiguredClass
{
    public function config($string) {
        $string = $this->transformConfigKey($string);
        return config('sso.'.$string);
    }

    private function transformConfigKey($key) {
        // private url default to server_url
        if ($key === 'server_private_url' && empty(config($key))) {
            return 'server_url';
        }
        return $key;
    }

    public function route($string) {
        return route('sso.'.$string);
    }
}