<?php

namespace Aviator\Helpdesk\Controllers\Admin;

use Aviator\Helpdesk\Traits\InteractsWithUsers;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Validation\Rule;
use Aviator\Helpdesk\Models\Team;
use Aviator\Helpdesk\Models\Agent;
use Illuminate\Routing\Controller;
use Aviator\Helpdesk\Models\Ticket;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Foundation\Validation\ValidatesRequests;

class AgentsController extends Controller
{
    use ValidatesRequests, InteractsWithUsers;

    /**
     * AgentsController constructor.
     */
    public function __construct()
    {
        $this->middleware([
            'auth',
            'helpdesk.supervisors',
        ]);

        $this->setUserConfig();
    }

    /**
     * Display a listing of the resource.
     * @return View
     */
    public function index()
    {
        $users = $this->fetchUsers();

//        dd(Agent::with('user', 'teams')->get());

        return view('helpdesk::admin.agents.index')->with([
            'agents' => Agent::with('user', 'teams')->get(),
            'users' => $users,
            'email' => $this->userModelEmailColumn,
            'isSuper' => true,
            'tab' => 'admin',
        ]);
    }

    /**
     * Display the specified resource.
     * @param  int  $id
     * @return View
     */
    public function show($id)
    {
        $agent = Agent::with('user', 'teams')->findOrFail($id);

        $tickets = Ticket::with('content')
            ->whereHas('assignment', function (Builder $query) use ($agent) {
                $query->where('assigned_to', $agent->id);
            })->get();

        return view('helpdesk::admin.agents.show')->with([
            'agent' => $agent,
            'tickets' => $tickets,
            'teams' => Team::all(),
            'isSuper' => true,
            'tab' => 'admin',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * @return RedirectResponse
     */
    public function store()
    {
        $agentsTable = config('helpdesk.tables.agents');
        $usersTable = config('helpdesk.tables.users');

        $this->validate(request(), [
            'user_id' => [
                'required',
                Rule::unique($agentsTable)->where(function (QueryBuilder $query) {
                    $query->whereNull('deleted_at');
                }),
                Rule::exists($usersTable, 'id'),
            ],
        ]);

        $agent = Agent::query()->create([
            'user_id' => request('user_id'),
        ]);

        return redirect(route('helpdesk.admin.agents.show', $agent->id));
    }

    /**
     * Remove the specified resource from storage.
     * @param  int  $id
     * @return RedirectResponse
     */
    public function destroy($id)
    {
        $this->validate(request(), [
            'delete_agent_confirmed' => 'required|in:1',
        ]);

        $super = Agent::where('user_id', auth()->user()->id)->first();

        // Super can't delete themselves
        $agent = Agent::where('id', '!=', $super->id)->whereId($id)->firstOrFail();

        $agent->delete();

        return redirect(
            route('helpdesk.admin.agents.index')
        );
    }
}
