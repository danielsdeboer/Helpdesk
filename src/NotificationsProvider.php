<?php

namespace Aviator\Helpdesk;

use Aviator\Helpdesk\Interfaces\NotificationFactoryInterface;
use Illuminate\Support\ServiceProvider;

class NotificationsProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot ()
    {
    }

    /**
     * Register application bindings.
     */
    public function register ()
    {
        $this->app->bind(
            NotificationFactoryInterface::class,
            config('helpdesk.notificationFactory')
        );
    }
}
