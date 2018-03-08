<?php

namespace Aviator\Helpdesk;

use Illuminate\Support\ServiceProvider;
use Aviator\Helpdesk\Factories\NotificationFactory;
use Aviator\Helpdesk\Interfaces\NotificationFactoryInterface;

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
        $this->app->singleton(
            NotificationFactoryInterface::class,
            function () {
                return new NotificationFactory(
                    config('helpdesk.notification'),
                    config('helpdesk.notifications')
                );
            }
        );
    }
}
