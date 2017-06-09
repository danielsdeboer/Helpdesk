<?php

namespace Aviator\Helpdesk\Controllers\Supervisor;

use Illuminate\Routing\Controller;

class AgentController extends Controller
{
    /**
     * Who is the reponse for.
     * @var string
     */
    protected $for;

    /**
     * Construct with agents only middleware.
     */
    public function __construct()
    {
        $this->middleware('helpdesk.supervisors');
    }

    /**
     * Display an index of the resource.
     * @return Response
     */
    public function index()
    {
    }
}
