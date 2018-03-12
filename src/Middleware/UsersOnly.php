<?php

namespace Aviator\Helpdesk\Middleware;

use Closure;
use Illuminate\Http\Request;

class UsersOnly
{
    /**
     * Handle an incoming request.
     * @param Request $request
     * @param \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (! $request->user()) {
            return redirect('login');
        }

        if (! $request->user()->agent) {
            return $next($request);
        }

        return abort(403, 'You are not permitted to access this resource.');
    }
}
