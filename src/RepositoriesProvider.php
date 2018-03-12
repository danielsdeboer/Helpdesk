<?php

namespace Aviator\Helpdesk;

use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Repositories\TicketsRepository;
use Illuminate\Support\ServiceProvider;

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
