<?php

namespace Aviator\Helpdesk\Middleware;

use Closure;
use Aviator\Helpdesk\Models\Agent;
use Illuminate\Support\Facades\Auth;

class DashboardRouter
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
        if (Auth::guest()) {
            return redirect(
                route('login')
            );
        }

        if ($request->user()->{$this->emailColumn} == $this->supervisorEmail) {
            return redirect(
                route('helpdesk.dashboard.supervisor')
            );
        }

        if (Agent::where('user_id', $request->user()->id)->first()) {
            return redirect(
                route('helpdesk.dashboard.agent')
            );
        }

        return redirect(
            route('helpdesk.dashboard.user')
        );
    }
}
