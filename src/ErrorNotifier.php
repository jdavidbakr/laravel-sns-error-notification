<?php
namespace jdavidbakr\LaravelSNSErrorNotification;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Cache;
use Request;
use AWS;
use Carbon\Carbon;

class ErrorNotifier extends ExceptionHandler {

	/**
	 * Report or log an exception.
	 *
	 * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
	 *
	 * @param  \Exception  $e
	 * @return void
	 */
	public function report(Exception $e)
	{
        if(!config('app.debug') && $this->shouldReport($e)) {
            if ($this->isHttpException($e)) {
                $status_code = $e->getStatusCode();
            } else {
                $status_code = $e->getCode();
            }
            Cache::remember('suppress-error-notification:'.$status_code.':'.$e->getFile().':'.$e->getLine(),
                Carbon::now()->addHours(config('sns-error-notification.cache-hours')),
                function() use($e, $status_code) {
                    // Build the "trace" variable
                    if(method_exists($e, 'getTrace')) {
                        $all_trace = $e->getTrace();
                        $trace_items = [];
                        foreach($all_trace as $item) {
                            $trace_items[] = "    ".json_encode($item);
                        }
                        if(count($trace_items)) {
                            $trace = implode("\n\n",$trace_items);
                        } else {
                            $trace = '(No trace data)';
                        }
                    } else {
                        $trace = '(exception does not have a trace method)';
                    }
                    $message = $e->getMessage();
                    if(!$message) {
                        $message = "Error in ".$e->getFile();
                    }

                    $sns = AWS::createClient('sns');
                    $result = $sns->publish([
                        'Message' => "URL: ".Request::fullUrl()."\n"
                            ."Method: ".Request::method()."\n"
                            ."Message: ".$message."\n"
                            ."Error Code: ".$status_code."\n"
                            ."File: ".$e->getFile()."\n"
                            ."Line: ".$e->getLine()."\n\n"
                            ."User ID: ".(auth()->check()?auth()->User()->id:'Guest')."\n\n"
                            ."Session: ".json_encode(session()->all())."\n\n"
                            ."Request: ".json_encode(request()->all())."\n\n"
                            ."Trace: ".$trace,
                        'Subject' => config('sns-error-notification.notification-subject'),
                        'TopicArn' => config('sns-error-notification.sns-topic'),
                    ]);
                    return true;
                });
        }
        return parent::report($e);
	}
}
