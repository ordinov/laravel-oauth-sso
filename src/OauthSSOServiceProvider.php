<?php
namespace Ordinov\OauthSSO;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Collection;
use Ordinov\OauthSSO\Macros\PreloadSSOData;

class OauthSSOServiceProvider extends ServiceProvider 
{
    public function register()
    {
        require_once(__DIR__.'/helpers/functions.php');

        Collection::make($this->macros())
            ->reject(fn ($class, $macro) => Collection::hasMacro($macro))
            ->each(fn ($class, $macro) => Collection::macro($macro, app($class)()));
    }

    public function macros(): array
    {
        return [
            'preloadSSOData' => PreloadSSOData::class,
            'withSSOData' => PreloadSSOData::class,
        ];
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/sso.php' => config_path('sso.php'),
        ]);

        $this->loadRoutesFrom(__DIR__.'/routes/web.php');

        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

    }

}