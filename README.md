<p align="center">
    <a href="https://asciito.coms" target="_blank"><img src="https://raw.githubusercontent.com/asciito/art/master/asciito_site/no_background/banner.png" width="400" alt="Laravel Logo"></a>
</p>

<p align="center">
    <a href="https://github.com/asciito/site/actions"><img src="https://github.com/asciito/site/workflows/blog-testing/badge.svg" alt="Build Status"></a>
    <a href="https://github.com/asciito/site/blob/main/LICENSE.md"><img src="https://img.shields.io/github/license/asciito/site" alt="License"></a>
    <a href="https://forge.laravel.com"><img src="https://img.shields.io/endpoint?url=https%3A%2F%2Fforge.laravel.com%2Fsite-badges%2F6195dfa0-5f93-4b42-818c-e2919980d250%3Fdate%3D1&style=flat-square" alt="Laravel Forge Site Deployment Status"/></a>
</p>

## About the Project

This project is my personal blog, open-sourced, built with [**Laravel**](https://laravel.com), [**FilamentPHP**](https://filamentphp.com) and **love** ❤️

### Premium Partners

- **[COYOTITO](https://coyotito.com.mx/)**

## Installation

This project can be installed in the same way as any other **Laravel** application. After initializing the project, follow the next few steps to set it up completely.

### Create a User

Before trying to log in, you need to create a user. To do that, run the command `php artisan make:filament-user`. Follow the instructions to create a new user.

**Note**:
If you're deploying the project, you need to define a new `env` variable to allow the new user to access the panel.

e.g.
```dotenv
SITE_ALLOWED_EMAILS=john@doe.com,jane@doe.com
```
Add the allowed emails in a CSV-like style, and only the defined emails can access the webtools panel.

> By default, in non-production environments, this is not necessary.

### Define the Webtools Path

By default, the webtools path is `webtools/`, but you can overwrite this by adding the `WEBTOOLS_PATH` variable to the `.env` file like so:

```dotenv
WEBTOOLS_PATH=<custom_path>
```

Now, every time you want to access the webtools panel, you must go to `example.com/<custom_path>`.


### Queue Worker

Once all the configuration is done, create a queue worker as required by [spatie/laravel-media-library](https://github.com/spatie/laravel-medialibrary)

## Documentation

_Working on it_...

## Contributing

Thank you for considering contributing to the project! The contribution guide can be found in the Project documentation.

## Security Vulnerabilities

If you discover a security vulnerability within the project, please send an e-mail to Ayax Córdova via [ayax.cordova@aydev.mx](mailto:ayax.cordova@aydev.mx). All security vulnerabilities will be promptly addressed.

## License

The Blog Project is open-sourced software licensed under the [MIT license](LICENSE.md).
