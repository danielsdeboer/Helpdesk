<?php

namespace Aviator\Helpdesk;

use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Repositories\TicketsRepository;
use Illuminate\Support\ServiceProvider;

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
