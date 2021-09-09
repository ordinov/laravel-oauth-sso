# Oauth SSO Laravel Package

## Installation:

Use [composer](https://getcomposer.org/) to install the package.

```bash
composer require ordinov/oauth-sso
```

Publish the 'sso' configuration. This command will publish a new cofig/sso.php file.

```bash
php artisan vendor:publish
```

Then select:

> Provider: Ordinov\OauthSSO\OauthSSOServiceProvider

## Configuration:
You will need to create the following lines into your .env file:

```bash
SSO_SERVER_URL= [SSO server URL]
SSO_CLIENT_ID= [this application client_id]
SSO_CLIENT_SECRET= [this application secret]
```

## License
[MIT](https://choosealicense.com/licenses/mit/)