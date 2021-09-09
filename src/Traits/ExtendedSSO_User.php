<?php

namespace Ordinov\OauthSSO\Traits;
use Ordinov\OauthSSO\Controllers\SSOController;
use \Carbon\Carbon;
use Session;

trait ExtendedSSO_User
{
    public function getSsoDataAttribute() {

        if (Session::has('ssoAuthData')) {
            if (Session::get('ssoAuthData')->since
                ->gte(Carbon::now()->subMinutes(config('sso.refresh_user_data_after_minutes')))
            ) {
                return Session::get('ssoAuthData');
            }
        }

        $userData = (new SSOController)->getUserData(Request(), false);
        $userData->since = Carbon::now();
        Session::forget('ssoAuthData');
        Session::put('ssoAuthData', $userData);

        return $userData;
    }
}