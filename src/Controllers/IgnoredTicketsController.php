<?php

namespace Aviator\Helpdesk\Controllers;

use Illuminate\Routing\Controller;
use Aviator\Helpdesk\Repositories\TicketsRepository;

class IgnoredTicketsController extends Controller
{
    /** @var array */
    protected $relations = [
        'user',
        'content',
    ];

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display an index of the resource.
     * @param \Aviator\Helpdesk\Repositories\TicketsRepository $tickets
     * @return \Illuminate\Contracts\View\View
     * @throws \InvalidArgumentException
     */
    public function index (TicketsRepository $tickets)
    {
        if (isset(auth()->user()->agent->is_super) && auth()->user()->agent->is_super) {
            return view('helpdesk::tickets.ignored.index')->with([
                'ignored' => $tickets->with($this->relations)->ignored()->paginate(),
            ]);
        }

        abort(404);
    }
}
