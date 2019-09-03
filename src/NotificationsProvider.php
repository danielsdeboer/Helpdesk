<?php

namespace Aviator\Helpdesk;

use Aviator\Helpdesk\Factories\NotificationFactory;
use Aviator\Helpdesk\Interfaces\NotificationFactoryInterface;
use Illuminate\Support\ServiceProvider;

class NotificationsProvider extends ServiceProvider
{
    /** @var bool */
    protected $defer = true;

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
