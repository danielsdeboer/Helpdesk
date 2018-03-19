<?php

namespace Aviator\Helpdesk\Controllers\Tickets;

use Aviator\Helpdesk\Repositories\TicketsRepository;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class PermalinkController extends Controller
{
    /**
     * Display a instance of the resource.
     * @param TicketsRepository $tickets
     * @param string $permalink
     * @return View
     */
    public function show(TicketsRepository $tickets, string $permalink)
    {
        $ticket = $tickets->permalink($permalink)->first();

        return view('helpdesk::tickets.show')->with([
            'ticket' => $ticket,
            'agents' => [],
            'collaborators' => [],
        ]);
    }
}
