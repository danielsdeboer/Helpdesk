<?php

namespace Aviator\Helpdesk\Controllers\Admin;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Pool;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;

class TeamMembersController extends Controller
{
    use ValidatesRequests;

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
     * Add an agent to a team
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request)
    {
        $this->validate($request, [
            'agent_id' => [
                'required',
                'int',
                Rule::exists(config('helpdesk.tables.agents'), 'id'),
            ],

            'team_id' => [
                'required',
                'int',
                Rule::exists(config('helpdesk.tables.pools'), 'id'),
            ],
            'from' => [
                'required',
                Rule::in(['agent', 'team']),
            ]
        ]);

        $agent = Agent::find($request->agent_id);
        $team = Pool::find($request->team_id);

        try {
            $agent->addToTeam($team);
        } catch (QueryException $e) {
            return redirect()->back()->withErrors(['agentInTeam', 'The agent is already in this team.']);
        }

        if ($request->from == 'agent') {
            return redirect( route('helpdesk.admin.agents.show', $request->agent_id) );
        }

        return redirect( route('helpdesk.admin.teams.show', $request->team_id) );
    }

    /**
     * Remove an agent from a team
     *
     * @return \Illuminate\Http\Response
     */
    public function remove(Request $request)
    {
        $this->validate($request, [
            'agent_id' => [
                'required',
                'int',
                Rule::exists(config('helpdesk.tables.agents'), 'id'),
            ],

            'team_id' => [
                'required',
                'int',
                Rule::exists(config('helpdesk.tables.pools'), 'id'),
            ],
            'from' => [
                'required',
                Rule::in(['agent', 'team']),
            ]
        ]);

        $agent = Agent::find($request->agent_id);
        $team = Pool::find($request->team_id);

        $agent->removeFromTeam($team);

        if ($request->from == 'agent') {
            return redirect( route('helpdesk.admin.agents.show', $request->agent_id) );
        }

        return redirect( route('helpdesk.admin.teams.show', $request->team_id) );
    }
}
