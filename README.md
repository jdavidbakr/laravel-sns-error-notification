# LaravelSNSErrorNotification

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][link-downloads]

This package is a simple extension of the Laravel Exception Handler that sends a notification via AWS SNS whenever there is an error, with helpful information like the URL that was called and the stack trace.  The notification is cached so that it only sends one notice per unique error message every 24 hours, and only fires if the application is not in debug mode (so you aren't innundated with error messages while working on your project).

## Install

Via Composer

``` bash
$ composer require jdavidbakr/laravel-sns-error-notification
```

Add the service provider to your config/app.php file:

```
jdavidbakr\LaravelSNSErrorNotification\LaravelSNSErrorNotificationServiceProvider::class,
```

If you haven't already set up to use AWS, you will need to install the service provider:

```
Aws\Laravel\AwsServiceProvider::class
```

as well as the Facade in the 'aliases' array:

```
'AWS' => Aws\Laravel\AwsFacade::class,
```

Install the config file

```
php artisan vendor:publish 
```

This inserts a config file at config/sns-error-notification.php.  You must set the SNS topic and subject in there.  Also note that you will need to configure the config/aws.php file as needed to give access to the SNS topic.

## Usage

To use, you will need to change the app/Exceptions/Handler.php class to extend \jdavidbakr\LaravelSNSErrorNotification\ErrorNotifier instead of \Illuminate\Foundation\Exceptions\Handler.  The easiest way is to remove this line:

```
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
```

and replace it with this:

```
use jdavidbakr\LaravelSNSErrorNotification\ErrorNotifier as ExceptionHandler;
```

## Testing

``` bash
$ phpunit
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Security

If you discover any security related issues, please email me@jdavidbaker.com instead of using the issue tracker.

## Credits

- [J David Baker][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/jdavidbakr/laravel-sns-error-notification.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/jdavidbakr/laravel-sns-error-notification/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/jdavidbakr/laravel-sns-error-notification.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/jdavidbakr/laravel-sns-error-notification.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/jdavidbakr/laravel-sns-error-notification.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/jdavidbakr/laravel-sns-error-notification
[link-travis]: https://travis-ci.org/jdavidbakr/laravel-sns-error-notification
[link-scrutinizer]: https://scrutinizer-ci.com/g/jdavidbakr/laravel-sns-error-notification/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/jdavidbakr/laravel-sns-error-notification
[link-downloads]: https://packagist.org/packages/jdavidbakr/laravel-sns-error-notification
[link-author]: https://github.com/jdavidbakr
[link-contributors]: ../../contributors
