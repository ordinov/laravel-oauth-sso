<?php

namespace Ordinov\OauthSSO\Macros;
use Ordinov\OauthSSO\OauthSSO;

class PreloadSSOData
{
    public function __invoke()
    {
        return function ($allValues = false) {
            $sso = new OauthSSO;

            //  skip empty
            if ($this->isEmpty()) { return $this; }
            
            $first = $this->first();
            
            // exclude non User models
            if (!$allValues) {
                $userClass = $sso->config('user_class');
                if(!$first instanceof $userClass) { return $this; }
            } else {
                if ((is_object($first) && !isset($first->id)) 
                    || (is_array($first) && !isset($first['id']))) {
                    return $this;
                }
            }

            // $ids = array_diff($this->pluck('sso_id')->toArray(), [ auth()->user()->sso_id ]);
            $ids = $this->pluck('id', 'sso_id')->toArray();
            $data = $sso->provider->getUsers(array_keys($ids));
            $keyedData = [];
            foreach ($data as $v) {
                $keyedData[$ids[$v->id]] = $v;
            }

            if (cache()->has('ssoCollectionData')) {
                $keyedData = $keyedData + cache('ssoCollectionData');
            }
            
            cache()->put('ssoCollectionData', $keyedData);

            return $this;
        };
    }
}