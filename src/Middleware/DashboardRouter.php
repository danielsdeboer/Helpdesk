<?php

namespace Aviator\Helpdesk\Middleware;

use Aviator\Helpdesk\Models\Agent;
use Illuminate\Contracts\Auth\Factory as Auth;
use Closure;

class DashboardRouter
{
    /**
     * The authentication factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

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
     * @param \Illuminate\Contracts\Auth\Factory $auth
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
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
        $this->auth->authenticate();

        if ($request->user()->{$this->emailColumn} == $this->supervisorEmail) {
            return redirect( route('helpdesk.dashboard.supervisor') );
        }

        if (Agent::where('user_id', $request->user()->id)->first()) {
            return redirect( route('helpdesk.dashboard.agent') );
        }

        return redirect( route('helpdesk.dashboard.user') );
    }
}