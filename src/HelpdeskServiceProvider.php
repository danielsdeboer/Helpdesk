<?php

namespace Aviator\Helpdesk;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;
use Illuminate\Foundation\Http\Kernel;
use Illuminate\Support\ServiceProvider;

class HelpdeskServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     * @param Kernel $kernel
     * @param Router $router
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
        $this->registerCommands();
        $this->publishImages();

        Blade::directive('para', function ($var) {
            return "<?php echo nl2br($var); ?>";
        });
    }

    /**
     * Push the middleware into the kernel middleware stack
     * and make them available via an alias.
     * @param  Kernel $kernel
     * @param  Router $router
     * @return void
     */
    protected function pushMiddleware($kernel, $router)
    {
        $kernel->pushMiddleware(\Aviator\Helpdesk\Middleware\AgentsOnly::class);
        $router->aliasMiddleware('helpdesk.agents', \Aviator\Helpdesk\Middleware\AgentsOnly::class);

        $kernel->pushMiddleware(\Aviator\Helpdesk\Middleware\SupervisorsOnly::class);
        $router->aliasMiddleware('helpdesk.supervisors', \Aviator\Helpdesk\Middleware\SupervisorsOnly::class);

        $kernel->pushMiddleware(\Aviator\Helpdesk\Middleware\DashboardRedirector::class);
        $router->aliasMiddleware('helpdesk.redirect.dashboard', \Aviator\Helpdesk\Middleware\DashboardRedirector::class);

        $kernel->pushMiddleware(\Aviator\Helpdesk\Middleware\TicketOwnerOrAssignee::class);
        $router->aliasMiddleware('helpdesk.ticket.owner', \Aviator\Helpdesk\Middleware\TicketOwnerOrAssignee::class);

        $kernel->pushMiddleware(\Aviator\Helpdesk\Middleware\TicketAssignee::class);
        $router->aliasMiddleware('helpdesk.ticket.assignee', \Aviator\Helpdesk\Middleware\TicketAssignee::class);
    }

    /**
     * Make the configuration file available for publishing.
     * @return void
     */
    protected function publishConfig()
    {
        $this->publishes([
            __DIR__ . '/../resources/config/helpdesk.php' => config_path('helpdesk.php'),
        ], 'config');
    }

    /**
     * Make the helpdesk factory available for publishing.
     * @return void
     */
    protected function publishFactories()
    {
        $this->publishes([
            __DIR__ . '/../resources/factories/HelpdeskFactory.php' => database_path('factories/HelpdeskFactory.php'),
        ], 'factories');
    }

    /**
     * Register model observers.
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

    /**
     * Register artisan commands.
     * @return void
     */
    public function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Aviator\Helpdesk\Commands\MakeSupervisor::class,
            ]);
        }
    }

    /**
     * Publish avatar images.
     * @return void
     */
    protected function publishImages()
    {
        $this->publishes([
            __DIR__ . '/../resources/images' => public_path('vendor/aviator'),
        ], 'public');
    }
}
