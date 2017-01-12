<?php

namespace Aviator\Helpdesk\Controllers\Admin;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Pool;
use Aviator\Helpdesk\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TeamsController extends Controller
{

    /**
     * Add middleware
     */
    public function __construct()
    {
        $this->middleware([
            'auth',
            'helpdesk.supervisors',
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $userModel = config('helpdesk.userModel');
        $email = config('helpdesk.userModelEmailColumn');

        return view('helpdesk::admin.teams.index')->with([
            'teams' => Pool::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $agent = Agent::findOrFail($id);
        $tickets = Ticket::whereHas('assignment', function($query) use ($agent) {
            $query->where('assigned_to', $agent->id);
        })->get();

        return view('helpdesk::admin.agents.show')->with([
            'agent' => $agent,
            'tickets' => $tickets,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
