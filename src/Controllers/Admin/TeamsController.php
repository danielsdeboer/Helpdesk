<?php

namespace Aviator\Helpdesk\Controllers\Admin;

use Illuminate\Http\Request;
use Aviator\Helpdesk\Models\Team;
use Aviator\Helpdesk\Models\Agent;
use Illuminate\Routing\Controller;
use Aviator\Helpdesk\Models\Ticket;
use Illuminate\Foundation\Validation\ValidatesRequests;

class TeamsController extends Controller
{
    use ValidatesRequests;

    /**
     * Add middleware.
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
            'teams' => Team::all(),
            'isSuper' => true,
            'tab' => 'admin',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->val($request);

        $team = Team::create([
            'name' => $request->name,
        ]);

        return $this->toShow($team);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $team = Team::findOrFail($id);

        $tickets = Ticket::whereHas('teamAssignment', function ($query) use ($team) {
            $query->where('team_id', $team->id);
        })->get();

        // Get all agents who are not assigned to this team (or who
        // are assigned to no team)
        $agents = Agent::with('user')->doesntHave('teams', 'or', function ($query) use ($team) {
            $query->where('team_id', $team->id);
        })->get();

        return view('helpdesk::admin.teams.show')->with([
            'team' => $team,
            'tickets' => $tickets,
            'agents' => $agents,
            'isSuper' => true,
            'tab' => 'admin',
        ]);
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
        $this->val($request);

        $team = Team::findOrFail($id);

        $team->update([
            'name' => $request->name,
        ]);

        return $this->toShow($team);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $this->validate($request, [
            'delete_team_confirmed' => 'required|in:1',
        ]);

        $team = Team::findOrFail($id);

        $team->delete();

        return redirect(route('helpdesk.admin.teams.index'));
    }

    /**
     * Perform request validation.
     * @param  Request $request
     * @return void
     */
    protected function val(Request $request)
    {
        $this->validate($request, [
            'name' => [
                'required',
            ],
        ]);
    }

    /**
     * Redirect to the show route with param.
     * @param  Team   $team
     * @return Response
     */
    protected function toShow(Team $team)
    {
        return redirect(route('helpdesk.admin.teams.show', $team->id));
    }
}
