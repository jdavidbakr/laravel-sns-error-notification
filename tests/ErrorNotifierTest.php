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
    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();

        $this->artisan('migrate', ['--database' => 'testing']);
    }

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
     * @test
     */
    public function testReport()
    {
        $mock = new AwsMock;
        app()->bind('aws', function() use($mock) {
            return $mock;
        });
    	\Config::set('app.debug', false);
        \Config::set('sns-error-notification.notification-subject','Test Subject');
        \Config::set('sns-error-notification.sns-topic','Topic:Arn');
        \Config::set('sns-error-notification.save-to-db',true);
    	$handler = new Notifier(new Logger('foo'));
        $message = str_random(32);
        $status = rand(100,600);
    	$exception = new HttpException($status, $message, null, [], 500);
        try {
            $handler->report($exception);
        } catch(HttpException $e) {
            // Ignore the exception we just created so we can test to make sure we sent the message
        }
        $data = $mock->lastPublish;
        $this->assertRegexp("/Message: {$message}/", $data['Message']);
        $this->assertRegexp("/Error Code: {$status}/", $data['Message']);
        $this->assertEquals('Test Subject', $data['Subject']);
        $this->assertEquals('Topic:Arn', $data['TopicArn']);

        $this->assertDatabaseHas('app_error_logs',[
                'message'=>$message,
                'error_code'=>$status,
            ]);
    }
}
