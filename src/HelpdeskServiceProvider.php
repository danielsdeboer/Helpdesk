<?php

namespace Aviator\Helpdesk;

use Aviator\Helpdesk\Commands\CreateSuper;
use Aviator\Helpdesk\Middleware\AgentsOnly;
use Aviator\Helpdesk\Middleware\DashboardRouter;
use Aviator\Helpdesk\Middleware\TicketAccess;
use Aviator\Helpdesk\Middleware\SupersOnly;
use Aviator\Helpdesk\Middleware\TicketAssignee;
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
use Aviator\Helpdesk\Models\TeamAssignment;
use Aviator\Helpdesk\Observers\NoteObserver;
use Aviator\Helpdesk\Observers\ReplyObserver;
use Aviator\Helpdesk\Observers\TicketObserver;
use Aviator\Helpdesk\Observers\ClosingObserver;
use Aviator\Helpdesk\Observers\DueDateObserver;
use Aviator\Helpdesk\Observers\OpeningObserver;
use Aviator\Helpdesk\Observers\AssignmentObserver;
use Aviator\Helpdesk\Observers\CollaboratorObserver;
use Aviator\Helpdesk\Observers\teamAssignmentObserver;

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
        $this->publishImages();

        Blade::directive('para', function ($var) {
            return "<?php echo nl2br($var); ?>";
        });

        $this->commands([
            CreateSuper::class
        ]);
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
        $kernel->pushMiddleware(AgentsOnly::class);
        $router->aliasMiddleware('helpdesk.agents', AgentsOnly::class);

        $kernel->pushMiddleware(SupersOnly::class);
        $router->aliasMiddleware('helpdesk.supervisors', SupersOnly::class);

        $kernel->pushMiddleware(DashboardRouter::class);
        $router->aliasMiddleware('helpdesk.redirect.dashboard', DashboardRouter::class);

        $kernel->pushMiddleware(TicketAccess::class);
        $router->aliasMiddleware('helpdesk.ticket.owner', TicketAccess::class);

        $kernel->pushMiddleware(TicketAssignee::class);
        $router->aliasMiddleware('helpdesk.ticket.assignee', TicketAssignee::class);
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
        TeamAssignment::observe(teamAssignmentObserver::class);
        Closing::observe(ClosingObserver::class);
        Opening::observe(OpeningObserver::class);
        Note::observe(NoteObserver::class);
        Collaborator::observe(CollaboratorObserver::class);
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
