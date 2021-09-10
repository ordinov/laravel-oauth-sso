<?php

namespace Ordinov\OauthSSO\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \GuzzleHttp\Client;
use Auth;
use Ordinov\OauthSSO\OauthSSO;

class SSOController extends Controller
{

    public $userClass = 'App\Models\User';
    protected $sso;

    public function __construct() {
        $this->sso = new OauthSSO;
        $this->userClass = $this->sso->config('user_class');
    }

    public function getLogin(Request $request)
    {
        return $this->sso->redirect->toLogIn();
    }

    public function doLogout(Request $request) {
        Auth::logout();
        return $this->sso->redirect->toLogOut();
    }

    public function loggedOut(Request $request)
    {
        return $this->sso->redirect->unauthenticated();
    }

    public function getCallback(Request $request)
    {
        $state = $request->session()->pull('state');
        throw_unless(strlen($state) > 0 && $state = $request->state, \InvalidArgumentException::class);

        $response = $this->sso->client->post('oauth/token', [
            'grant_type' => 'authorization_code',
            'client_id' => $this->sso->config('client_id'),
            'client_secret' => $this->sso->config('client_secret'),
            'redirect_uri' => $this->sso->route('callback'),
            'code' => $request->code
        ], false);

        if (!isset($response->access_token)) {
            return $response;
        }

        $request->session()->put(json_decode(json_encode($response), true));

        return $this->connectUser($request);
    }

    public function getUserData(Request $request) {
        return $this->sso->provider->getUser();
    }

    public function connectUser(Request $request)
    {
        $userData = $this->verifyUser($this->sso->provider->getUser());

        if ($userData === null) {
            return $this->sso->redirect->unauthenticated();
        }
        if ($userData instanceof \Illuminate\Http\RedirectResponse) {
            return $userData;
        }

        // get curret User from email or create a new one
        $user = $this->userClass::where('sso_id', $userData->id)->first() ?? new $this->userClass;
        
        // update local db user and session
        $sync = sso_sync_user_data($user, $userData);

        // login user
        Auth::login($user);

        // create a "welcome" key in session for consuming alerts
        $request->session()->put('welcome', true);

        // return to defined route (see /config/sso.php)
        return $this->sso->redirect->authenticated();
    }

    public function verifyUser($userData) {

        if (!$userData) { return null; }

        // active verification
        if (isset($userData->is_active) && (bool)$userData->is_active === false) {
            return $this->sso->redirect->unauthenticated();
        }

        $mustVerify = ['email','phone'];
        foreach ($mustVerify as $k => $v) {
            if (!(bool)$this->sso->config('user_must_verify_'.$v) 
                || $userData->{$v.'_verified_at'}) {
                    unset($mustVerify[$k]);
            }
        }
        
        if (!empty($mustVerify)) {
            return $this->sso->redirect->toVerification($mustVerify);
        }

        return $userData;
    }
}
