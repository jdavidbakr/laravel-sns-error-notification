<?php

namespace jdavidbakr\LaravelSNSErrorNotification;

use Illuminate\Support\ServiceProvider;

class LaravelSNSErrorNotificationServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/sns-error-notification.php' => config_path('sns-error-notification.php')
        ], 'config');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}