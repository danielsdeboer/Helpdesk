<?php

namespace Aviator\Helpdesk;

use Aviator\Helpdesk\Middleware\AgentsOnly;
use Aviator\Helpdesk\Middleware\SupervisorsOnly;
use Illuminate\Foundation\Http\Kernel;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class HelpdeskServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(Kernel $kernel, Router $router)
    {
        $this->pushMiddleware($kernel, $router);
        $this->publishConfig();
        $this->publishFactories();

        $this->loadMigrationsFrom(__DIR__ . '/../resources/migrations');

        $this->mergeConfigFrom(
            __DIR__ . '/../resources/config/helpdesk.php',
            'helpdesk'
        );

        $this->registerObservers();
    }

    /**
     * Push the middleware into the kernel middleware stack
     * and make them available via an alias
     * @param  Kernel $kernel
     * @param  Router $router
     * @return void
     */
    protected function pushMiddleware($kernel, $router)
    {
        $kernel->pushMiddleware(AgentsOnly::class);
        $router->middleware('helpdesk.agents', AgentsOnly::class);

        $kernel->pushMiddleware(SupervisorsOnly::class);
        $router->middleware('helpdesk.supervisors', SupervisorsOnly::class);
    }

    /**
     * Make the configuration file available for publishing
     * @return void
     */
    protected function publishConfig()
    {
        $this->publishes([
            __DIR__ . '/../resources/config/helpdesk.php' => config_path('helpdesk.php'),
        ], 'config');
    }

    /**
     * Make the helpdesk factory available for publishing
     * @return void
     */
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