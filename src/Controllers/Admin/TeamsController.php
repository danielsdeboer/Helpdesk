<?php

namespace Aviator\Helpdesk\Controllers\Admin;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Team;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Requests\Admin\Teams\StoreRequest;
use Aviator\Helpdesk\Requests\Admin\Teams\UpdateRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TeamsController extends Controller
{
    use ValidatesRequests;

    /**
     * Add middleware.
     */
    public function __construct ()
    {
        $this->middleware([
            'auth',
            'helpdesk.supervisors',
        ]);
    }

    public function index (): View
    {
        return view('helpdesk::admin.teams.index')->with([
            'teams' => Team::all(),
            'isSuper' => true,
            'tab' => 'admin',
        ]);
    }

    public function show (int $id): View
    {
        $team = Team::findOrFail($id);

        $tickets = Ticket::query()
            ->whereHas(
                'teamAssignment',
                function ($query) use ($team) {
                    $query->where('team_id', $team->id);
                }
            )
            ->get();

        // Get all agents who are not assigned to this team (or who
        // are assigned to no team)
        $agents = Agent::with('user')
            ->doesntHave(
                'teams',
                'or',
                function ($query) use ($team) {
                    $query->where('team_id', $team->id);
                }
            )
            ->get();

        return view('helpdesk::admin.teams.show')->with([
            'team' => $team,
            'tickets' => $tickets,
            'agents' => $agents,
            'isSuper' => true,
            'tab' => 'admin',
        ]);
    }

    public function store (StoreRequest $request): RedirectResponse
    {
        $team = Team::create($request->validated());

        return $this->toShow($team);
    }

    public function update (UpdateRequest $request, int $id): RedirectResponse
    {
        $team = Team::findOrFail($id);

        $team->update($request->validated());

        return $this->toShow($team);
    }

    public function destroy (Request $request, int $id): RedirectResponse
    {
        $this->validate($request, ['delete_team_confirmed' => 'required|in:1']);

        $team = Team::findOrFail($id);

        $team->delete();

        return redirect()->route('helpdesk.admin.teams.index');
    }

    protected function toShow (Team $team): RedirectResponse
    {
        return redirect()->route('helpdesk.admin.teams.show', $team->id);
    }
}
