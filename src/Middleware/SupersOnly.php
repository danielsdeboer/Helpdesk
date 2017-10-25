<?php

namespace Aviator\Helpdesk\Middleware;

use Closure;

class SupersOnly
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
        if ($request->user() && $request->user()->agent && $request->user()->agent->isSuper()) {
            return $next($request);
        }

        return abort(403, 'You are not permitted to access this resource.');
    }
}
