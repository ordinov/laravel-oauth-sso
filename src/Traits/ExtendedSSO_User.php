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

    /**
     * Add user data attribute
     *
     * @return object
     */
    public function getSsoDataAttribute() {
        $sso = new OauthSSO;
        return $sso->provider->getCachedUser($this);
    }
}