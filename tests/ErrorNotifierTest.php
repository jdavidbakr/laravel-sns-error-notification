<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Container\Container as Logger;

class ErrorNotifierTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testReport()
    {
    	\Config::set('app.debug', false);
    	$handler = new \jdavidbakr\LaravelSNSErrorNotification\ErrorNotifier(new Logger('foo'));
    	$exception = new HttpException(500, 'Test exception', null, [], 500);
    	$handler->report($exception);
    }
}
