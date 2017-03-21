<?php

namespace jdavidbakr\LaravelSNSErrorNotification;

use Illuminate\Database\Eloquent\Model;

class AppErrorLog extends Model
{
    protected $fillable = [
    	'url',
    	'method',
    	'message',
    	'error_code',
    	'file',
    	'line',
    	'user_id',
    	'session',
    	'request',
    	'trace',
    ];
    protected $casts = [
    	'session'=>'collection',
    	'request'=>'collection',
    ];
}
