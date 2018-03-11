<?php

namespace Aviator\Helpdesk;

use Aviator\Helpdesk\Models\Ticket;
use Illuminate\Support\ServiceProvider;
use Aviator\Helpdesk\Repositories\TicketsRepository;

class RepositoriesProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot ()
    {
        $this->app->bind(
            TicketsRepository::class,
            function () {
                return new TicketsRepository(new Ticket, auth()->user());
            }
        );
    }
}
