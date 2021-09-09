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

Example: 

```php
$userVerifiedSince = $user->sso_data->email_verified_at;
$user_phone = $user->sso_data->phone;
```

Still you can access actual db data, comparing with remote provider data
```php
$db_email = $user->email; // local db
$provider_email = $user->sso_data->email; // from sso authentication
```

DB data are stored in session and resynced every X minutes (defined in `config/sso.php` file, default `10`). 

You can get the last update timestamp accessing the `since` attribute;
```php
$user->sso_data->since;
```

## Configuration:
Create the following lines into your .env file:

```bash
SSO_SERVER_URL= [Remote SSO server URL, example https://auth.mydomain.com]
SSO_CLIENT_ID= [this application client_id]
SSO_CLIENT_SECRET= [this application secret]
```

## License
[MIT](https://choosealicense.com/licenses/mit/)