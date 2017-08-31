<?php

namespace Aviator\Helpdesk\Middleware;

use Aviator\Helpdesk\Models\Ticket;
use Closure;
use Aviator\Helpdesk\Models\Agent;
use Illuminate\Http\Request;

class OwnerOrAssigneeOnly
{
    /**
     * The supervisor's email.
     * @var array
     */
    protected $supervisorEmails = [];

    /**
     * The user model's email column.
     * @var string
     */
    protected $emailColumn;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->supervisorEmails = config('helpdesk.supervisors');
        $this->emailColumn = config('helpdesk.userModelEmailColumn');
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!isset($request->user()->id)) {
            return abort(403);
        }

        $ticket = $this->getTicket($request);

        $agent = Agent::query()
            ->where('user_id', $request->user()->id)
            ->first();

        /*
         * A supervisor can access any ticket.
         */
        if (in_array($request->user()->{$this->emailColumn}, $this->supervisorEmails)) {
            return $next($request);
        }

        /*
         * The ticket creator can access this ticket.
         */
        if ($ticket->user_id == $request->user()->id) {
            return $next($request);
        }

        /*
         * The collaborators can access this ticket.
         */
        if ($agent && $ticket->collaborators && $ticket->isCollaborator($agent)) {
            return $next($request);
        }

        /*
         * The team lead for this team can access this ticket.
         */
        if ($agent && $ticket->poolAssignment && $agent->isMemberOf($ticket->poolAssignment->pool)) {
            return $next($request);
        }

        /*
         * The assigned agent can access this ticket.
         */
        if ($agent && $ticket->assignment && $ticket->assignment->assigned_to == $agent->id) {
            return $next($request);
        }

        /*
         * Abort if all else fails.
         */
        return abort(403);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Aviator\Helpdesk\Models\Ticket
     */
    protected function getTicket (Request $request)
    {
        /** @var \Aviator\Helpdesk\Models\Ticket $ticket */
        $ticket = $request->route('ticket');

        if ($ticket instanceOf Ticket) {
            return $ticket;
        }

        /** @var \Aviator\Helpdesk\Models\Ticket $ticket */
        $ticket = Ticket::query()->find($ticket);

        if ($ticket) {
            return $ticket;
        }

        abort(403);
    }
}
