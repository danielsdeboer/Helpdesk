<?php

namespace Aviator\Helpdesk\Middleware;

use Aviator\Helpdesk\Models\Ticket;
use Closure;
use Illuminate\Http\Request;

class TicketAccess
{
    /**
     * Handle an incoming request.
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /*
         * Deny guests
         */
        if (!$request->user()) {
            return abort(403);
        }

        $ticket = $this->getTicketFromRequest($request);

        /** @var \Aviator\Helpdesk\Models\Agent $agent */
        $agent = $request->user()->agent;

        /*
         * Run agent checks first
         */
        if ($agent) {
            /*
             * Supers may view any ticket.
             */
            if ($agent->isSuper()) {
                return $next($request);
            }

            /*
             * The assignee can view the ticket.
             */
            if ($ticket->status()->assignedTo($agent)) {
                return $next($request);
            }

            /*
             * Collaborators can view the ticket
             */
            if ($ticket->status()->collaborates($agent)) {
                return $next($request);
            }

            /*
             * Team leads can view tickets assigned to their team.
             */
            if ($agent->teamLeads) {
                foreach ($agent->teamLeads as $team) {
                    if ($team->id === $ticket->teamAssignment->team->id) {
                        return $next($request);
                    }
                }
            }
        }

        /*
         * The ticket creator can access this ticket.
         */
        if ($ticket->status()->ownedBy($request->user())) {
            return $next($request);
        }

        return abort(403);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Aviator\Helpdesk\Models\Ticket
     */
    protected function getTicketFromRequest(Request $request)
    {
        /** @var \Aviator\Helpdesk\Models\Ticket $ticket */
        $ticket = $request->route('ticket') instanceof Ticket
            ? $request->route('ticket')
            : Ticket::query()->find($request->route('ticket'));

        return $ticket ?: abort(403);
    }
}
