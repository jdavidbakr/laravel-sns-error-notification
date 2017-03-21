<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\HttpKernel\Exception\HttpException;
use jdavidbakr\LaravelSNSErrorNotification\LaravelSNSErrorNotificationServiceProvider as Service;
use jdavidbakr\LaravelSNSErrorNotification\ErrorNotifier as Notifier;
use Illuminate\Container\Container as Logger;
use jdavidbakr\LaravelSNSErrorNotification\Mocks\AwsMock;

class ErrorNotifierTest extends Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            Service::class,
            \Aws\Laravel\AwsServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'AWS' => \Aws\Laravel\AwsFacade::class
        ];
    }

    /**
     * A basic test example.
     *
     * @return void
     * @expectedException Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function testReport()
    {
        $mock = new AwsMock;
        app()->bind('aws', function() use($mock) {
            return $mock;
        });
    	\Config::set('app.debug', false);
    	$handler = new Notifier(new Logger('foo'));
    	$exception = new HttpException(500, 'Test exception', null, [], 500);
    	$handler->report($exception);
    }
}
