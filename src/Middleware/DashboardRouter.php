<?php

namespace Aviator\Helpdesk\Middleware;

use Closure;

class DashboardRouter
{
    /**
     * Handle an incoming request.
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /*
         * Guests must log in.
         */
        if (auth()->guest()) {
            return redirect(
                route('login')
            );
        }

        /*
         * Supervisors get redirected to their dashboard.
         */
        if (auth()->user()->agent && auth()->user()->agent->isSuper()) {
            return redirect(
                route('helpdesk.dashboard.supervisor')
            );
        }

        /*
         * Agents get redirected to their dashboard.
         */
        if (auth()->user()->agent) {
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
