<?php

namespace Aviator\Helpdesk;

use Aviator\Helpdesk\Middleware\AgentsOnly;
use Aviator\Helpdesk\Middleware\DashboardRouter;
use Aviator\Helpdesk\Middleware\SupersOnly;
use Aviator\Helpdesk\Middleware\TicketAccess;
use Aviator\Helpdesk\Middleware\TicketAssignee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\Kernel;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class MiddlewaresProvider extends ServiceProvider
{
    /** @var array */
    private $middlewares = [
        'helpdesk.agents' => AgentsOnly::class,
        'helpdesk.supervisors' => SupersOnly::class,
        'helpdesk.redirect.dashboard' => DashboardRouter::class,
        'helpdesk.ticket.owner' => TicketAccess::class,
        'helpdesk.ticket.assignee' => TicketAssignee::class,
    ];

    /**
     * Bootstrap the application services.
     * @param Kernel $kernel
     * @param Router $router
     */
    public function boot (Kernel $kernel, Router $router)
    {
        /** @var Model $model */
        foreach ($this->middlewares as $alias => $class){
            $kernel->pushMiddleware($class);
            $router->aliasMiddleware($alias, $class);
        }
    }
}
