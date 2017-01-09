<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Pool;
use Aviator\Helpdesk\Models\Ticket;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AgentDashboardTest extends TestCase
{
    /**
     * @group feature
     * @group dashboard
     * @test
     */
    public function a_user_cant_visit_the_agent_dashboard()
    {
        $this->be(factory(User::class)->create());

        $this->call('GET', 'helpdesk/dashboard/agent');

        $this->assertResponseStatus(403);
    }

    /**
     * @group feature
     * @group dashboard
     * @test
     */
    public function an_agent_can_visit_their_dashboard()
    {
        $this->be(factory(Agent::class)->create()->user);

        $response = $this->call('GET', 'helpdesk/dashboard/agent');

        $this->assertResponseOk();
    }

    /**
     * @group feature
     * @group dashboard
     * @test
     */
    public function the_dashboard_returns_the_correct_json_structure()
    {
        $this->be(factory(Agent::class)->create()->user);

        $response = $this->call('get', 'helpdesk/dashboard/agent');

        $this->assertResponseOk();
        $this->seeJsonStructure([
            'team',
            'overdue',
            'agent',
        ]);
    }

    /**
     * @group feature
     * @group dashboard
     * @test
     */
    public function the_dashboard_returns_the_agents_tickets()
    {
        $agent = factory(Agent::class)->create();
        $agentTickets = factory(Ticket::class, 10)->create()->each(function($item) use ($agent) {
            $item->assignToAgent($agent);
        });

        $this->be($agent->user);
        $response = $this->call('get', 'helpdesk/dashboard/agent');

        $this->assertResponseOk();
        $this->seeJsonSubset([
            'agent' => $agentTickets->toArray(),
        ]);
    }

    /**
     * @group feature
     * @group dashboard
     * @test
     */
    public function the_dashboard_returns_the_tickets_assigned_to_the_agents_team()
    {
        $agent = factory(Agent::class)->create();
        $team = factory(Pool::class)->create();
        $agent->addToTeam($team);
        $nonTeamTickets = factory(Ticket::class, 10)->create();
        $teamTickets = factory(Ticket::class, 10)->create()->each(function($item) use ($team) {
            $item->assignToPool($team);
        });

        $this->be($agent->user);
        $response = $this->call('get', 'helpdesk/dashboard/agent');

        $this->assertResponseOk();
        $this->seeJsonSubset([
            'team' => $teamTickets->toArray(),
            'teamCount' => 10,
        ]);
    }

    /**
     * @group feature
     * @group dashboard
     * @test
     */
    public function the_dashboard_returns_overdue_tickets_for_the_agent_or_their_teams()
    {
        // Create the agent and team and assign
        $agent = factory(Agent::class)->create();
        $team = factory(Pool::class)->create();
        $agent->addToTeam($team);

        // Create and assign tickets to agent
        $assignedTickets = factory(Ticket::class, 10)->create()->each(function($item) use ($agent) {
            $item->assignToAgent($agent);
        });

        // Create and assign tickets to team
        $teamTickets = factory(Ticket::class, 10)->create()->each(function($item) use ($team) {
            $item->assignToPool($team);
        });

        // Merge the ticket collections and make them overdue
        $allTickets = $assignedTickets->merge($teamTickets)->each(function($item) {
            $item->dueOn('yesterday');
        });

        $this->be($agent->user);
        $response = $this->call('get', 'helpdesk/dashboard/agent');

        $this->assertResponseOk();
        $this->seeJsonSubset([
            'overdue' => $allTickets->toArray(),
            'overdueCount' => 20,
        ]);
    }
}