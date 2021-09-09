# Oauth SSO Laravel Package

## Installation:

Use [composer](https://getcomposer.org/) to install the package.

```bash
composer require ordinov/oauth-sso
```

Request Laravel to publish the configuration.

```bash
php artisan vendor:publish
```

Then select:

> Provider: Ordinov\OauthSSO\OauthSSOServiceProvider

This command will publish a new `cofig/sso.php` file.

You may need to edit the `User` model (App/Models/User.php) adding the `ExtendedSSO_User` trait, and extending the `$appends` property:
```php
<?php
namespace App\Models;
# < ..... >
use Ordinov\OauthSSO\Traits\ExtendedSSO_User;

class User extends Authenticatable
{
    use HasApiTokens, 
        # add this:
        ExtendedSSO_User;

    # .. and this:
    protected $appends = ['sso_data'];
}
```

This will lately allow you to get all the user informations 
from the SSO provider with `$user->sso_data`.

## How does it works:

This package register some routes to redirect all the authentication capabilities to an external SSO provider implementing OAuth2.0 authentication.

You will just need for some basic user informations in the `users` table in your application db, such as:
- id
- email
- laravel timestamps

All the other informations will be stored in the authentication provider application.

This will be pretty useful if you have more then one service and don't want to write all the authentication process every time. This will also avoid storing the same user multiple times so that the user will be able to log-in all your applications just being logged in the main sso provider. This is how SSO works, precisely.

If you want to add some more informations into your local `users` table, just add the column names into the `$fillable` property, accordingly to Laravel best practices.

```php
class User extends Authenticable
{
    protected $fillable = [
        'email',
        'address',
        'city'
        // 'country' - not included in this array
        // 'postcode' - not included in this array
    ];
}
```

Still you can access actual db data, comparing with remote provider data
```php
$user->email || $user->sso_data->email; // local db AND remote
$user->address || $user->sso_data->address; // local db AND remote
$user->city || $user->sso_data->city; // local db AND remote
$user->sso_data->country; // excluded from local db
$user->sso_data->postcode; // excluded from local db
```

SSO Provider data are stored in session and resynced every X minutes (defined in `config/sso.php` file, default `10`). 

You can get the last update timestamp accessing the `since` attribute;
```php
$user->sso_data->since;
```


```php
$user->email;
$user->address;
$user->city;
$user->sso_data->country;
$user->sso_data->postcode;
```

Attributes can be added later on, and they will be synced from the SSO provider during the next user auth request.

## Configuration:
Create the following lines into your .env file:

```bash
SSO_SERVER_URL= [Remote SSO server URL, example https://auth.mydomain.com]
SSO_CLIENT_ID= [this application client_id]
SSO_CLIENT_SECRET= [this application secret]
```

## License
[MIT](https://choosealicense.com/licenses/mit/)