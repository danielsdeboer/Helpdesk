<?php

namespace Aviator\Helpdesk;

use Aviator\Helpdesk\Middleware\TicketsRedirector;
use Illuminate\Routing\Router;
use Illuminate\Foundation\Http\Kernel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Aviator\Helpdesk\Middleware\UsersOnly;
use Aviator\Helpdesk\Middleware\AgentsOnly;
use Aviator\Helpdesk\Middleware\SupersOnly;
use Aviator\Helpdesk\Middleware\TicketAccess;
use Aviator\Helpdesk\Middleware\DashboardRedirector;

class MiddlewaresProvider extends ServiceProvider
{
    /** @var array */
    private $middlewares = [
        'helpdesk.users' => UsersOnly::class,
        'helpdesk.agents' => AgentsOnly::class,
        'helpdesk.supervisors' => SupersOnly::class,
        'helpdesk.redirect.dashboard' => DashboardRedirector::class,
        'helpdesk.redirect.tickets' => TicketsRedirector::class,
        'helpdesk.ticket.owner' => TicketAccess::class,
    ];

    /**
     * Bootstrap the application services.
     * @param Kernel $kernel
     * @param Router $router
     */
    public function boot (Kernel $kernel, Router $router)
    {
        /* @var Model $model */
        foreach ($this->middlewares as $alias => $class) {
            $kernel->pushMiddleware($class);
            $router->aliasMiddleware($alias, $class);
        }
    }
}
