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
        $this->publishConfig();
        $this->publishFactories();

        $this->loadMigrationsFrom(__DIR__ . '/../resources/migrations');

        $this->mergeConfigFrom(
            __DIR__ . '/../resources/config/helpdesk.php',
            'helpdesk'
        );

        $this->registerObservers();
    }

    protected function publishConfig()
    {
        $this->publishes([
            __DIR__ . '/../resources/config/helpdesk.php' => config_path('helpdesk.php'),
        ], 'config');
    }

    protected function publishFactories()
    {
        $this->publishes([
            __DIR__ . '/../resources/factories/HelpdeskFactory.php' => database_path('factories/HelpdeskFactory.php'),
        ], 'factories');
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
        \Aviator\Helpdesk\Models\Reply::observe(\Aviator\Helpdesk\Observers\ReplyObserver::class);
        \Aviator\Helpdesk\Models\PoolAssignment::observe(\Aviator\Helpdesk\Observers\PoolAssignmentObserver::class);
        \Aviator\Helpdesk\Models\Closing::observe(\Aviator\Helpdesk\Observers\ClosingObserver::class);
        \Aviator\Helpdesk\Models\Opening::observe(\Aviator\Helpdesk\Observers\OpeningObserver::class);
        \Aviator\Helpdesk\Models\Note::observe(\Aviator\Helpdesk\Observers\NoteObserver::class);
    }
}