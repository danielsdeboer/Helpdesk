<?php

namespace Aviator\Helpdesk;

use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Repositories\TicketsRepository;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class RepositoriesProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->bind(
            TicketsRepository::class,
            function () {
                return new TicketsRepository(new Ticket(), auth()->user());
            }
        );
    }

    public function provides(): array
    {
        return [TicketsRepository::class];
    }
}
