<?php

namespace Aviator\Helpdesk\Controllers\Dashboard;

use Illuminate\Routing\Controller;

class UserController extends Controller
{
    /**
     * Construct with agents only middleware
     */
    public function __construct()
    {
        $this->middleware('helpdesk.agents');
    }

    /**
     * Display an index of the controller
     * @return Response
     */
    public function index()
    {
        return [
            'unassigned' => Ticket::with('user', 'opening')->unassigned()->get(),
            'overdue' => Ticket::with('user', 'dueDate')->overdue()->get()->sortBy(function($item) {
                return $item->dueDate->due_on;
            }),
            'dueToday' => Ticket::with('user')->dueToday()->get(),
            'open' => Ticket::with('user')->opened()->get()->sortBy(function($item) {
                return isset($item->dueDate->due_on) ? $item->dueDate->due_on->toDateString() : 9999999;
            }),
            'headerTab' => 'dashboard',
        ];
    }
}
