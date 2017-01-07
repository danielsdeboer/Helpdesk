<?php

namespace Aviator\Helpdesk\Middleware;

use Aviator\Helpdesk\Models\Agent;
use Closure;

class SupervisorsOnly
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
        $email = config('helpdesk.userModelEmailColumn');

        if ($request->user() && $request->user()->$email == config('helpdesk.supervisor.email')) {
            return $next($request);
        }

        abort(403, 'You are not permitted to access this resource.');
    }
}