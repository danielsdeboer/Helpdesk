<?php

namespace Aviator\Helpdesk\Middleware;

use Closure;
use Illuminate\Http\Request;

class DashboardRedirector
{
    /**
     * Handle an incoming request.
     * @param Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        /*
         * Guests must log in.
         */
        if (!$request->user()) {
            return redirect(
                route('login')
            );
        }

        /*
         * Supervisors get redirected to their dashboard.
         */
        if ($request->user()->agent && $request->user()->agent->isSuper()) {
            return redirect(
                route('helpdesk.dashboard.supervisor')
            );
        }

        /*
         * Agents get redirected to their dashboard.
         */
        if ($request->user()->agent) {
            return redirect(
                route('helpdesk.dashboard.agent')
            );
        }

        /*
         * Finally, everyone else gets the user dashboard.
         */
        return redirect(
            route('helpdesk.dashboard.user')
        );
    }
}
