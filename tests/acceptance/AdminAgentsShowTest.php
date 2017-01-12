<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Pool;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Tests\User;
use Illuminate\Support\Facades\Route;

class AdminAgentsShowTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        Route::any('login', function() {
            return;
        });
    }

    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.agent
     * @group acc.admin.agent.show
     * @test
     */
    public function guests_cant_visit()
    {
        $this->call('GET', 'helpdesk/admin/agents/1');

        $this->assertResponseStatus('302');
        $this->assertRedirectedTo('login');

    }

    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.agent
     * @group acc.admin.agent.show
     * @test
     */
    public function users_cant_visit()
    {
        $user = factory(User::class)->create();

        $this->be($user);
        $this->call('GET', 'helpdesk/admin/agents/1');

        $this->assertResponseStatus('403');
    }

    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.agent
     * @group acc.admin.agent.show
     * @test
     */
    public function agents_cant_visit()
    {
        $user = factory(Agent::class)->create()->user;

        $this->be($user);
        $this->call('GET', 'helpdesk/admin/agents/1');

        $this->assertResponseStatus('403');
    }

    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.agent
     * @group acc.admin.agent.show
     * @test
     */
    public function supervisors_can_visit()
    {
        $user = factory(Agent::class)->states('isSuper')->create()->user;
        $agent = factory(Agent::class)->create();

        $this->be($user);
        $this->visit('helpdesk/admin/agents/2');

        $this->see('id="tab-admin-agents"')
            ->see('<strong>' . $agent->user->name . '</strong>')
            ->see('<h2 class="subtitle">Added on ' . $agent->created_at->toDateString() . '</h2>')
            ->see('In 0 teams')
            ->see('0 open tickets');
    }

    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.agent
     * @group acc.admin.agent.show
     * @test
     */
    public function it_has_a_list_of_the_agents_teams()
    {
        $user = factory(Agent::class)->states('isSuper')->create()->user;
        $team = factory(Pool::class)->create();
        $team2 = factory(Pool::class)->create();
        $agent = factory(Agent::class)->create()->addToTeams([$team, $team2]);

        $this->be($user);
        $this->visit('helpdesk/admin/agents/2');

        $this->see('<a href="http://localhost/helpdesk/admin/teams/1">' . $team->name . '</a>')
            ->see('<a href="http://localhost/helpdesk/admin/teams/2">' . $team2->name . '</a>');
    }

    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.agent
     * @group acc.admin.agent.show
     * @test
     */
    public function it_has_a_list_of_the_agents_open_tickets()
    {
        $super = factory(Agent::class)->states('isSuper')->create()->user;
        $agent = factory(Agent::class)->create();
        $agent2 = factory(Agent::class)->create();

        $ticket1 = factory(Ticket::class)->create()->assignToAgent($agent);
        $ticket2 = factory(Ticket::class)->create()->assignToAgent($agent);
        $ticket3 = factory(Ticket::class)->create()->assignToAgent($agent2);

        $this->be($super);
        $this->visit('helpdesk/admin/agents/2');

        $this->see('<a href="http://localhost/helpdesk/tickets/1">' . $ticket1->content->title . '</a>')
            ->see('<a href="http://localhost/helpdesk/tickets/2">' . $ticket2->content->title . '</a>')
            ->dontSee('<a href="http://localhost/helpdesk/tickets/3">' . $ticket3->content->title . '</a>');
    }

}