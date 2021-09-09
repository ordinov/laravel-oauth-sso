<?php

namespace Ordinov\OauthSSO;
use Illuminate\Support\Facades\Facade;

class OauthSSOFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return OauthSSO::class;
    }
}