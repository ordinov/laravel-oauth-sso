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
            'state' => $state,
            'sso_origin' => $this->route('login'),
            'sso_ip' => request()->ip(),
            'registered_redirect_to' => $this->route('login')
        ]);
        return redirect($this->config('server_url').'/oauth/authorize?' . $query);
    }

    public function toLogOut() : \Illuminate\Http\RedirectResponse
    {
        return redirect(
            $this->config('server_url')
            .'/logout?redirect_to='
            .route( $this->config('redirect_unauthenticated_to_route') )
        );
    }

    public function toVerification($mustVerify = [])
    {
        if (empty($mustVerify)) { return redirect($this->route('login')); }
        return redirect(
            $this->config('server_url')
            .'/'.implode('',$mustVerify).'-verification'
            .'?verified_redirect_to='.$this->route('login')
        );
    }
}
