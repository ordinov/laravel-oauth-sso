<?php

namespace Ordinov\OauthSSO\Traits;
use Ordinov\OauthSSO\OauthSSO;
use Session;
use Cache;

trait ExtendedSSO_User
{
    /**
     * This method is called upon instantiation of the Eloquent Model.
     * @return void
     */
    public function initializeExtendedSSO_User()
    {
        $this->fillable[] = 'sso_id';
        $this->appends[] = 'sso_data';
        foreach (config('sso.injected_sso_fields') as $fieldName) {
            $this->appends[] = $fieldName;
        }
        if (config('sso.exclude_sso_data_from_serialization')) {
            $this->hidden[] = 'sso_data';
        }
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

    /**
     * Here we are with the magic method
     */
    public function __call($method, $params) {
        // can't override default
        if ( is_callable(['parent', '__call']) ) {
            return parent::__call($method, $params);
        }
        // get from sso is required
        $sso = new OauthSSO;
        foreach (config('sso.injected_sso_fields') as $fieldName) {
            if ($method === ('get'.implode('',array_map('ucfirst', explode('_', $fieldName))).'Attribute')) {
                return $sso->provider->getCachedUser($this)->{$fieldName};
            }
        }
    }
}