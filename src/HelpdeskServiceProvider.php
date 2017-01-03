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

        $this->registerObservers();
    }

    /**
     * Register model observers
     * @return void
     */
    protected function registerObservers()
    {
        \Aviator\Helpdesk\Models\Ticket::observe(\Aviator\Helpdesk\Observers\TicketObserver::class);
        \Aviator\Helpdesk\Models\Assignment::observe(\Aviator\Helpdesk\Observers\AssignmentObserver::class);
        \Aviator\Helpdesk\Models\DueDate::observe(\Aviator\Helpdesk\Observers\DueDateObserver::class);
        \Aviator\Helpdesk\Models\Email::observe(\Aviator\Helpdesk\Observers\EmailObserver::class);
        \Aviator\Helpdesk\Models\PoolAssignment::observe(\Aviator\Helpdesk\Observers\PoolAssignmentObserver::class);
        \Aviator\Helpdesk\Models\Closing::observe(\Aviator\Helpdesk\Observers\ClosingObserver::class);
    }
}