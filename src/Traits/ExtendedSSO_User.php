<?php

namespace Ordinov\OauthSSO\Traits;
use Ordinov\OauthSSO\OauthSSO;
use Session;
use Cache;

trait ExtendedSSO_User
{
    /**
     * This method is called upon instantiation of the Eloquent Model.
     * It adds the "seoMeta" field to the "$fillable" array of the model.
     *
     * @return void
     */
    public function initializeExtendedSSO_User()
    {
        $this->fillable[] = 'sso_id';
    }

    public function currentUserCanfetchSSOInformations() {
        return true;
    }

    /**
     * Add user data attribute
     *
     * @return object
     */
    public function getSsoDataAttribute() {

        if (!auth()->check()) { return new \StdClass; }

        $sso = new OauthSSO;

        // if is currently logged user..
        if ((int)$this->id === (int)auth()->id()) {

            if (Session::has('ssoAuthData')) {
                if (isset(Session::get('ssoAuthData')->synced_on) && Session::get('ssoAuthData')->synced_on
                    ->gte(now()->subMinutes(config('sso.refresh_user_data_after_minutes')))
                ) {
                    return Session::get('ssoAuthData');
                }
            }

            $userData = $sso->provider->getUser();
            sso_sync_user_data(auth()->user(), $userData);
            return $userData;
        }

        // if is any other user
        if (!$this->currentUserCanfetchSSOInformations()) { 
            throw new \Exception('You don\'t have permissions to fetch this users data.'); 
            return new \StdClass;
        }
        // check if we already have informations in session
        if (Cache::has('ssoCollectionData')) {
            $allData = Cache::get('ssoCollectionData');
            foreach ($allData as $k => $val) {
                if ($val->id === $this->id) {
                    return $val;
                }
            }
        }
        $userData = $sso->provider->getUser($this->sso_id);

        return $userData;
    }
}