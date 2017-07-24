<?php

namespace Aviator\Helpdesk\Middleware;

use Closure;
use Aviator\Helpdesk\Models\Agent;

class TicketAssignee
{
    /**
     * The supervisor's email.
     * @var string
     */
    protected $supervisorEmail;

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
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $ticket = $request->route('ticket');
        $agent = Agent::where('user_id', $request->user()->id)->first();

        /**
         * Supervisors can access any ticket
         */
        if (in_array($request->user()->{$this->emailColumn}, $this->supervisorEmails)) {
            return $next($request);
        }

        if ($agent && $ticket->assignment && $ticket->assignment->assigned_to == $agent->id) {
            return $next($request);
        }

        abort(403);
    }
}
