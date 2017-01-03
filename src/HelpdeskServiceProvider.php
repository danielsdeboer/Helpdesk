<?php

namespace Aviator\Helpdesk;

use Illuminate\Support\ServiceProvider;

class HelpdeskServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../resources/config/helpdesk.php' => $this->app->configPath() . '/' . 'helpdesk.php',
        ], 'config');

        $this->loadMigrationsFrom(__DIR__ . '../resources/migrations');

        $this->mergeConfigFrom(
            __DIR__ . '/../resources/config/helpdesk.php',
            'helpdesk'
        );
    }
}