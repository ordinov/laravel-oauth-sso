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

This command will publish a new cofig/sso.php file.
## Configuration:
You will need to create the following lines into your .env file:

```bash
SSO_SERVER_URL= [Remote SSO server URL, example https://auth.mydomain.com]
SSO_CLIENT_ID= [this application client_id]
SSO_CLIENT_SECRET= [this application secret]
```

## License
[MIT](https://choosealicense.com/licenses/mit/)