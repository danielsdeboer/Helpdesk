<?php

namespace Aviator\Helpdesk\Middleware;

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
        $supervisorEmails = config('helpdesk.supervisors');

        if ($request->user() && in_array($request->user()->$email, $supervisorEmails)) {
            return $next($request);
        }

        abort(403, 'You are not permitted to access this resource.');
    }
}
