<?php

namespace Aviator\Helpdesk\Controllers\Tickets;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Ticket;
use Illuminate\Routing\Controller;

class UuidController extends Controller
{
    /**
     * Display a instance of the resource
     * @param  Ticket $ticket
     * @return Reponse
     */
    public function show($uuid)
    {
        $ticket = Ticket::whereUuid($uuid)->firstOrFail();

        return view('helpdesk::tickets.show')->with([
            'ticket' => $ticket,
            'withOpen' => true,
            'withClose' => true,
            'withReply' => true,
        ]);
    }
}
