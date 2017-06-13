<?php

namespace Aviator\Helpdesk\Controllers;

use Illuminate\Routing\Controller;

class PublicController extends Controller
{
    /**
     * Display the splash page
     * @return Response
     */
    public function splash()
    {
        return view('helpdesk::splash.index');
    }

    /**
     * Redirect to the admin page
     * @return Response
     */
    public function redirectToAdmin()
    {
        return redirect(
            route('helpdesk.admin.agents.index')
        );
    }
}
