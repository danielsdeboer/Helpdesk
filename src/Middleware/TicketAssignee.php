<?php

namespace Aviator\Helpdesk\Middleware;

use Aviator\Helpdesk\Models\Agent;
use Closure;
use Illuminate\Support\Facades\Auth;

class TicketAssignee
{
    /**
     * The supervisor's email
     * @var string
     */
    protected $supervisorEmail;

    /**
     * The user model's email column
     * @var string
     */
    protected $emailColumn;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->supervisorEmail = config('helpdesk.supervisor.email');
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

        // Supervisor can access any ticket
        if ($request->user()->{$this->emailColumn} == $this->supervisorEmail) {
            return $next($request);
        }

        if ($agent && $ticket->assignment && $ticket->assignment->assigned_to == $agent->id) {
            return $next($request);
        }

        abort(403);
    }
}