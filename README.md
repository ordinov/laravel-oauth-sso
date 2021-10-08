# Oauth SSO Laravel Package

## Installation:

Use [composer](https://getcomposer.org/) to install the package.

```bash
composer require ordinov/laravel-oauth-sso
```

Request Laravel to publish the configuration.

```bash
php artisan vendor:publish --provider="Ordinov\OauthSSO\OauthSSOServiceProvider"
```

This command will publish a new `cofig/sso.php` file

```bash
php artisan migrate
```

This command will add the `users.sso_id` colum right after `users.id`


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
}
```

This will lately allow you to get all the user informations 
from the SSO provider with `$user->sso_data`.

## How does it work:

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
Attributes can be added later on, and they will be synced from the SSO provider during the next user auth request.

Still you can access actual db data, comparing with remote provider data
```php
$user->email || $user->sso_data->email; // local db AND remote
$user->address || $user->sso_data->address; // local db AND remote
$user->city || $user->sso_data->city; // local db AND remote
$user->sso_data->country; // excluded from local db
$user->sso_data->postcode; // excluded from local db
```

SSO Provider data are stored in session and resynced every X minutes (defined in `config/sso.php` file, default = `10` minutes). 

You can get the last sync timestamp accessing the `synced_on` attribute;
```php
$user->sso_data->synced_on; // Carbon object
```

```php
$user->email;
$user->address;
$user->city;
$user->sso_data->country;
$user->sso_data->postcode;
```

This package provies also a globally accessible `user()` function that returns the current logged in user
This is an example of what you get with `user()->toJSON()`:

```json
{
    "id":5,
    "email":"pippo@myemail.com",
    "created_at":"2021-09-09T14:59:27.000000Z",
    "updated_at":"2021-09-09T14:59:27.000000Z",
    "sso_data":{
        "id":1,
        "is_business":0,
        "companyname":"Pippo Franco S.r.l.",
        "first_name":"Pippo",
        "last_name":"Franco",
        "email":"pippo@myemail.com",
        "phone":"+39123456789",
        "pec":null,
        "address":"Via Di Qua",
        "city":"Roma",
        "province":"Roma",
        "country":"IT",
        "postcode":"00031",
        "cf":null,
        "piva":null,
        "email_verified_at":"2021-09-09T15:05:37.000000Z",
        "phone_verified_at":"2021-09-09T15:00:12.000000Z",
        "is_active":1,
        "created_at":"2021-09-03T17:55:05.000000Z",
        "updated_at":"2021-09-09T15:05:37.000000Z",
        "full_name":"Pippo Franco S.r.l.",
        "synced_on":"2021-09-09T17:06:27.973070Z"
    }
}
```

This is optimized for `collections` as well. When a collection (`Illuminate\Support\Collection`) of users is istanciated, you can call the `->withSSOData()` method on it. This will perform a single request to the SSO provider that returns the full informations for all the users in the collection, so that the client won't make a brand new request each time you get a `$user->sso_data` (for example in loops such as tables). Users data will be cached and retrieved when the `sso_data` attribute is called:
```php
/*
 * get a list of users from the database
 * and perform a request to the SSO provider to fetch all those users informations.
 */
$users = User::where('id', <= 100)->get()->withSSOData();
// loop em
foreach ($users as $user) {
    // sso_data is already available here
    // no other requests are fired to the SSO provider.
    dd($user->sso_data); 
}
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