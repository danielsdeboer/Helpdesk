<?php

namespace Aviator\Helpdesk\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Aviator\Helpdesk\Models\Team;
use Aviator\Helpdesk\Models\Agent;
use Illuminate\Routing\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Validation\ValidatesRequests;

class TeamMembersController extends Controller
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
     * Add an agent to a team.
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
                Rule::exists(config('helpdesk.tables.teams'), 'id'),
            ],
            'from' => [
                'required',
                Rule::in(['agent', 'team']),
            ],
        ]);

        $agent = Agent::find($request->agent_id);
        $team = Team::find($request->team_id);

        try {
            $agent->addToTeam($team);
        } catch (QueryException $e) {
            return redirect()->back()->withErrors(['agentInTeam', 'The agent is already in this team.']);
        }

        if (isset($request->team_lead)) {
            //Check if the agent is already a team lead.
            if($team->isTeamLead($agent)) {
                return redirect()->back()->withErrors(['The agent is already the lead of this team.']);
            }

            //Check if there is a team lead set.
            if ($team->teamLeads->first()) {
                $team->teamLeads->first()->removeTeamLeadOf($team);
            }

            $agent->makeTeamLeadOf($team);
        }

        if ($request->from == 'agent') {
            return redirect(route('helpdesk.admin.agents.show', $request->agent_id));
        }

        return redirect(route('helpdesk.admin.teams.show', $request->team_id));
    }

    /**
     * Remove an agent from a team.
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
                Rule::exists(config('helpdesk.tables.teams'), 'id'),
            ],
            'from' => [
                'required',
                Rule::in(['agent', 'team']),
            ],
        ]);

        $agent = Agent::find($request->agent_id);
        $team = Team::find($request->team_id);

        $agent->removeFromTeam($team);

        if ($request->from == 'agent') {
            return redirect(route('helpdesk.admin.agents.show', $request->agent_id));
        }

        return redirect(route('helpdesk.admin.teams.show', $request->team_id));
    }

    /**
     * Add an agent as the team lead.
     *
     * @return \Illuminate\Http\Response
     */
    public function makeTeamLead(Request $request)
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
                Rule::exists(config('helpdesk.tables.teams'), 'id'),
            ],
            'from' => [
                'required',
                Rule::in(['agent', 'team']),
            ],
        ]);

        $agent = Agent::find($request->agent_id);
        $team = Team::find($request->team_id);

        //Check if the agent is already a team lead.
        if ($team->isTeamLead($agent)) {
            return redirect()->back()->withErrors(['The agent is already the lead of this team.']);
        }

        //Check if there is a team lead set.
        if ($team->teamLeads->first()) {
            $team->teamLeads->first()->removeTeamLeadOf($team);
        }

        $agent->makeTeamLeadOf($team);

        return redirect(route('helpdesk.admin.teams.show', $request->team_id));
    }
}
