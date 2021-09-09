<?php
namespace Ordinov\OauthSSO;

use Illuminate\Support\ServiceProvider;

class OauthSSOServiceProvider extends ServiceProvider 
{
    public function register()
    {
        require_once(__DIR__.'/helpers/functions.php');
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/sso.php' => config_path('sso.php'),
        ]);

        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
    }
}