<?php

namespace Ordinov\OauthSSO;
use Ordinov\OauthSSO\ConfiguredClass;

class OauthSSORedirect extends ConfiguredClass
{
    public function __construct() {}

    public function authenticated($params = []) : \Illuminate\Http\RedirectResponse
    {
        return redirect(
            route($this->config('redirect_authenticated_to_route'))
            . (empty($params) ? '' : ('?'.http_build_query($params)) )
        );
    }

    public function unauthenticated($params = []) : \Illuminate\Http\RedirectResponse
    {
        return redirect(
            route($this->config('redirect_unauthenticated_to_route'))
            . (empty($params) ? '' : ('?'.http_build_query($params)) )
        );
    }

    public function toLogIn() : \Illuminate\Http\RedirectResponse
    {
        session()->put('state', $state = \Illuminate\Support\Str::random(40));
        $query = http_build_query([
            'client_id' => $this->config('client_id'),
            'redirect_uri' => $this->route('callback'),
            'response_type' => 'code',
            'scope' => 'view-user',
            'state' => $state,
            'registered_redirect_to' => $this->route('login')
        ]);
        return redirect($this->config('server').'/oauth/authorize?' . $query);
    }

    public function toLogOut() : \Illuminate\Http\RedirectResponse
    {
        return redirect(
            $this->config('server')
            .'/logout?redirect_to='
            .route( $this->config('redirect_unauthenticated_to_route') )
        );
    }

    public function toVerification($mustVerify = [])
    {
        if (empty($mustVerify)) { return redirect($this->route('login')); }
        return redirect(
            $this->config('server')
            .'/'.implode('',$mustVerify).'-verification'
            .'?verified_redirect_to='.$this->route('login')
        );
    }
}