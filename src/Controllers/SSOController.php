<?php

namespace Ordinov\OauthSSO\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use \GuzzleHttp\Client;
use \App\Models\User;
use Auth;

class SSOController extends Controller
{
    public function getSSOLoginPage(Request $request) {
        return $this->getLogin($request);
    }

    public function getSSORegisterPage(Request $request) {
        return $this->getLogin($request);
    }

    public function doLogout(Request $request) {
        Auth::logout();
        return redirect(config('sso.server').'/logout?redirect_to='.route(config('sso.redirect_unauthenticated_to_route')));
    }

    public function getLogin(Request $request)
    {
        $request->session()->put('state', $state = Str::random(40));
        $query = http_build_query([
            'client_id' => config('sso.client_id'),
            'redirect_uri' => route('sso.callback'),
            'response_type' => 'code',
            'scope' => 'view-user',
            'state' => $state,
            'registered_redirect_to' => route('login')
        ]);
        return redirect(config('sso.server').'/oauth/authorize?' . $query);
    }

    public function loggedOut(Request $request)
    {
        return redirect(route(config('sso.redirect_unauthenticated_to_route')));
    }

    public function getCallback(Request $request)
    {
        $state = $request->session()->pull('state');
        throw_unless(strlen($state) > 0 && $state = $request->state, \InvalidArgumentException::class);

        $guzzleClient = new Client([
            'base_uri' => env('SSO_SERVER_URL'),
            'verify' => config('sso.secure'),
        ]);
        
        try {
            $response = $guzzleClient->post('oauth/token', [
                'form_params' => [
                    'grant_type' => 'authorization_code',
                    'client_id' => config('sso.client_id'),
                    'client_secret' => config('sso.client_secret'),
                    'redirect_uri' => route('sso.callback'),
                    'code' => $request->code
                ]
            ]);
            $response = json_decode($response->getBody()->getContents(), true);
        } catch(\Exception $exception) {
            return json_encode($exception->getMessage());
        }

        $request->session()->put($response);
        return $this->connectUser($request);
    }

    public function getUserData(Request $request, $array = true) {
        $access_token = $request->session()->get('access_token');
        $guzzleClient = new Client([
            'base_uri' => config('sso.server'),
            'verify' => config('sso.secure'),
        ]);
        
        try {
            $response = $guzzleClient->get('api/user', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $access_token
                ],
                'query' => []
            ]);
            $response = json_decode($response->getBody()->getContents(), $array);
        } catch(\Exception $exception) {
            return $exception->getResponse()->getBody(true);
        }

        return $response;
    }

    public function connectUser(Request $request)
    {
        $userData = $this->verifyUser($this->getUserData($request));
        if ($userData instanceof \Illuminate\Http\RedirectResponse) {
            return $userData;
        }

        // get curret User from email or create a new one
        $user = User::where('email', $userData['email'])->first() ?? new User;
        
        // update current user informations based on current User model class structure
        $fields = $user->getFillable();
        foreach ($fields as $field) {
            if (isset($userData[$field])) {
                $user->fill([$field => $userData[$field]]);
            }
        }

        // save current user informations
        $user->save();

        // login user
        Auth::login($user);

        // create a "welcome" key in session for consuming alerts
        $request->session()->put('welcome', true);

        // return to defined route (see /config/sso.php)
        return redirect(route(config('sso.redirect_authenticated_to_route')));
    }

    public function verifyUser(array $userData) {

        // active verification
        if (array_key_exists('is_active', $userData) && (bool)$userData['is_active'] === false) {
            return redirect(route(config('sso.redirect_unauthenticated_to_route')));
        }

        $mustVerifyString = '';
        foreach (['email','phone'] as $verificationKey) {
            if ((bool)config('sso.user_must_verify_'.$verificationKey) && 
                $userData[$verificationKey.'_verified_at'] === null) {
                    $mustVerifyString .= $verificationKey;
            }
        }

        if ($mustVerifyString !== '') {
            return redirect(
                config('sso.server')
                .'/'.$mustVerifyString.'-verification'
                .'?verified_redirect_to='.route('login')
            );
        }

        return $userData;
    }
}
