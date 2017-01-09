<?php

namespace Aviator\Helpdesk\Controllers\Dashboard;

use Aviator\Helpdesk\Repositories\Tickets;
use Illuminate\Routing\Controller;

class UserController extends Controller
{
    /**
     * Construct with agents only middleware
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display an index of the controller
     * @return Response
     */
    public function index()
    {
        return [
            'user' => Tickets::forUser(auth()->user())->all(),
            'overdue' => Tickets::forUser(auth()->user())->overdue(),
        ];
    }
}
