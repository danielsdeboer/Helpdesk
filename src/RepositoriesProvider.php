<?php

namespace Aviator\Helpdesk;

use Aviator\Helpdesk\Models\Ticket;
use Illuminate\Support\ServiceProvider;
use Aviator\Helpdesk\Repositories\TicketsRepository;

class RepositoriesProvider extends ServiceProvider
{
    /** @var bool */
    protected $defer = true;

    /**
     * Register any application services.
     */
    public function register ()
    {
        $this->app->bind(
            TicketsRepository::class,
            function () {
                return new TicketsRepository(new Ticket, auth()->user());
            }
        );
    }
}
