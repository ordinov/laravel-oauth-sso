<?php

namespace Ordinov\OauthSSO;

class ConfiguredClass
{
    public function config($string) {
        return config('sso.'.$string);
    }

    public function route($string) {
        return route('sso.'.$string);
    }
}