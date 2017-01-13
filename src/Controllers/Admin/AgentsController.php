<?php

namespace Aviator\Helpdesk\Controllers\Admin;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Pool;
use Aviator\Helpdesk\Models\Ticket;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;

class AgentsController extends Controller
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $email = config('helpdesk.userModelEmailColumn');

        $users = $this->getUsers();

        return view('helpdesk::admin.agents.index')->with([
            'agents' => Agent::with('user', 'teams')->whereHas('user', function($query) use ($email) {

                $superEmail = config('helpdesk.supervisor.email');

                $query->where($email, '<>', $superEmail);
            })->get(),
            'users' => $users,
            'email' => $email,
            'isSuper' => true,
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
        $this->validate($request, [
            'user_id' => [
                'required',
                Rule::unique(config('helpdesk.tables.agents'))->where(function($query) {
                    $query->whereNull('deleted_at');
                }),
                Rule::exists(config('helpdesk.tables.users'), 'id'),
            ],
        ]);

        $agent = Agent::create([
            'user_id' => request()->user_id,
        ]);

        return redirect( route('helpdesk.admin.agents.show', $agent->id));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $agent = Agent::with('user', 'teams')->findOrFail($id);
        $tickets = Ticket::with('content')->whereHas('assignment', function($query) use ($agent) {
            $query->where('assigned_to', $agent->id);
        })->get();

        return view('helpdesk::admin.agents.show')->with([
            'agent' => $agent,
            'tickets' => $tickets,
            'teams' => Pool::all(),
            'isSuper' => true,
        ]);
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
            'delete_agent_confirmed' => 'required|in:1'
        ]);

        $super = Agent::where('user_id', auth()->user()->id)->first();

        // Super can't delete themselves
        $agent = Agent::where('id', '!=', $super->id)->whereId($id)->firstOrFail();

        $agent->delete();

        return redirect( route('helpdesk.admin.agents.index') );
    }

    protected function getUsers()
    {
        $userModel = config('helpdesk.userModel');

        if (! config('helpdesk.callbacks.user')) {
            return $userModel::all();
        }

        return $userModel::where(config('helpdesk.callbacks.user'))->get();
    }
}
