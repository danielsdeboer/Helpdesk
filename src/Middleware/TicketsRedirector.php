<?php

namespace Aviator\Helpdesk\Middleware;

use Closure;
use Illuminate\Http\Request;

class TicketsRedirector
{
    /**
     * Handle an incoming request.
     * @param Request $request
     * @param Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        /*
         * Guests may not access this route.
         */
        if (! $request->user()) {
            return redirect(
                route('login')
            );
        }

        /*
         * All agents (including supers) are redirected to the same page.
         */
        if ($request->user()->agent) {
            return redirect(
                route('helpdesk.agents.tickets.index')
            );
        }

        return $next($request);
    }
}
