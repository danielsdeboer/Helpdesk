<?php

namespace Aviator\Helpdesk;

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
        $this->loadMigrationsFrom(__DIR__ . '/../resources/migrations');

        $this->publishConfig();
        $this->mergeConfigFrom(
            __DIR__ . '/../resources/config/helpdesk.php',
            'helpdesk'
        );

        $this->registerObservers();

        $this->pushMiddleware($kernel, $router);

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'helpdesk');
        $this->loadRoutesFrom(__DIR__ . '/Routes/routes.php');

        $this->publishFactories();

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
        $kernel->pushMiddleware(\Aviator\Helpdesk\Middleware\AgentsOnly::class);
        $router->middleware('helpdesk.agents', \Aviator\Helpdesk\Middleware\AgentsOnly::class);

        $kernel->pushMiddleware(\Aviator\Helpdesk\Middleware\SupervisorsOnly::class);
        $router->middleware('helpdesk.supervisors', \Aviator\Helpdesk\Middleware\SupervisorsOnly::class);

        $kernel->pushMiddleware(\Aviator\Helpdesk\Middleware\DashboardRedirector::class);
        $router->middleware('helpdesk.redirect.dashboard', \Aviator\Helpdesk\Middleware\DashboardRedirector::class);

        $kernel->pushMiddleware(\Aviator\Helpdesk\Middleware\TicketOwnerOrAssignee::class);
        $router->middleware('helpdesk.ticket.owner', \Aviator\Helpdesk\Middleware\TicketOwnerOrAssignee::class);

        $kernel->pushMiddleware(\Aviator\Helpdesk\Middleware\TicketAssignee::class);
        $router->middleware('helpdesk.ticket.assignee', \Aviator\Helpdesk\Middleware\TicketAssignee::class);
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