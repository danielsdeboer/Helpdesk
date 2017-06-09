<?php

namespace Aviator\Helpdesk\Middleware;

use Closure;
use Aviator\Helpdesk\Models\Agent;

class AgentsOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->user() && Agent::where('user_id', $request->user()->id)->first()) {
            return $next($request);
        }

        abort(403, 'You are not permitted to access this resource.');
    }
}
