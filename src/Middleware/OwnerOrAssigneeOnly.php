<?php

namespace Aviator\Helpdesk\Middleware;

use Closure;
use Aviator\Helpdesk\Models\Agent;

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
        $ticket = $request->route('ticket');
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
        if ($agent && $ticket->collaborators && $ticket->collaborators->is($agent)) {
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
}
