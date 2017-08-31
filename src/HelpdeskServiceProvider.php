<?php

namespace Aviator\Helpdesk;

use Illuminate\Routing\Router;
use Aviator\Helpdesk\Models\Note;
use Aviator\Helpdesk\Models\Reply;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Models\Closing;
use Aviator\Helpdesk\Models\DueDate;
use Aviator\Helpdesk\Models\Opening;
use Illuminate\Support\Facades\Blade;
use Illuminate\Foundation\Http\Kernel;
use Aviator\Helpdesk\Models\Assignment;
use Illuminate\Support\ServiceProvider;
use Aviator\Helpdesk\Models\Collaborator;
use Aviator\Helpdesk\Models\PoolAssignment;
use Aviator\Helpdesk\Observers\NoteObserver;
use Aviator\Helpdesk\Observers\ReplyObserver;
use Aviator\Helpdesk\Observers\TicketObserver;
use Aviator\Helpdesk\Observers\ClosingObserver;
use Aviator\Helpdesk\Observers\DueDateObserver;
use Aviator\Helpdesk\Observers\OpeningObserver;
use Aviator\Helpdesk\Observers\AssignmentObserver;
use Aviator\Helpdesk\Observers\CollaboratorObserver;
use Aviator\Helpdesk\Observers\PoolAssignmentObserver;

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

        $kernel->pushMiddleware(\Aviator\Helpdesk\Middleware\OwnerOrAssigneeOnly::class);
        $router->aliasMiddleware('helpdesk.ticket.owner', \Aviator\Helpdesk\Middleware\OwnerOrAssigneeOnly::class);

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
        Ticket::observe(TicketObserver::class);
        Assignment::observe(AssignmentObserver::class);
        DueDate::observe(DueDateObserver::class);
        Reply::observe(ReplyObserver::class);
        PoolAssignment::observe(PoolAssignmentObserver::class);
        Closing::observe(ClosingObserver::class);
        Opening::observe(OpeningObserver::class);
        Note::observe(NoteObserver::class);
        Collaborator::observe(CollaboratorObserver::class);
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
