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
                    $trace = $this->getTrace($e);
                    $message = $this->getMessage($e);

                    $sns = AWS::createClient('sns');
                    $result = $sns->publish([
                        'Message' => "URL: ".Request::fullUrl()."\n"
                            ."Method: ".Request::method()."\n"
                            ."Message: ".$this->getMessage($e)."\n"
                            ."Error Code: ".$status_code."\n"
                            ."File: ".$e->getFile()."\n"
                            ."Line: ".$e->getLine()."\n\n"
                            ."User ID: ".(auth()->check()?auth()->User()->id:'Guest')."\n\n"
                            ."Session: ".json_encode(session()->all())."\n\n"
                            ."Request: ".json_encode(request()->all())."\n\n"
                            ."Trace: ".$this->getTrace($e),
                        'Subject' => config('sns-error-notification.notification-subject'),
                        'TopicArn' => config('sns-error-notification.sns-topic'),
                    ]);
                    return true;
                });
            if(config('sns-error-notification.save-to-db')) {
                AppErrorLog::create([
                        'url'=>Request::fullUrl(),
                        'method'=>Request::method(),
                        'message'=>$this->getMessage($e),
                        'error_code'=>$status_code,
                        'file'=>$e->getFile(),
                        'line'=>$e->getLine(),
                        'user_id'=>(auth()->check()?auth()->User()->id:'Guest'),
                        'session'=>session()->all(),
                        'request'=>request()->all(),
                        'trace'=>$this->getTrace($e),
                    ]);
            }
        }
        return parent::report($e);
	}

    protected function getTrace(Exception $e)
    {
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
        return $trace;
    }

    protected function getMessage(Exception $e)
    {
        $message = $e->getMessage();
        if(!$message) {
            $message = "Error in ".$e->getFile();
        }
        return $message;
    }
}
