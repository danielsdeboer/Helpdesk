<?php

namespace Aviator\Helpdesk;

use Aviator\Helpdesk\Factories\NotificationFactory;
use Aviator\Helpdesk\Interfaces\NotificationFactoryInterface;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class NotificationsProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register application bindings.
     */
    public function register()
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

    public function provides(): array
    {
        return [NotificationFactoryInterface::class];
    }
}
