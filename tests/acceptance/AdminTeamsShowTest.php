<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Pool;
use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Ticket;
use Illuminate\Support\Facades\Route;

class AdminTeamsShowTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        Route::any('login', function () {
        });
    }

    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.team
     * @group acc.admin.team.show
     * @test
     */
    public function guests_cant_visit()
    {
        $this->call('GET', 'helpdesk/admin/teams/1');

        $this->assertResponseStatus('302');
        $this->assertRedirectedTo('login');
    }

    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.team
     * @group acc.admin.team.show
     * @test
     */
    public function users_cant_visit()
    {
        $user = factory(User::class)->create();

        $this->be($user);
        $this->call('GET', 'helpdesk/admin/teams/1');

        $this->assertResponseStatus('403');
    }

    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.team
     * @group acc.admin.team.show
     * @test
     */
    public function agents_cant_visit()
    {
        $user = factory(Agent::class)->create()->user;

        $this->be($user);
        $this->call('GET', 'helpdesk/admin/teams/1');

        $this->assertResponseStatus('403');
    }

    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.team
     * @group acc.admin.team.show
     * @test
     */
    public function supervisors_can_visit()
    {
        $user = factory(Agent::class)->states('isSuper')->create()->user;
        $team = factory(Pool::class)->create();

        $this->be($user);
        $this->visit('helpdesk/admin/teams/1');

        $this->see('id="tab-admin-agents"')
            ->see('<strong>' . $team->name . '</strong>')
            ->see('0 open tickets');
    }

    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.team
     * @group acc.admin.team.show
     * @test
     */
    public function it_has_a_list_of_agents()
    {
        $super = factory(Agent::class)->states('isSuper')->create()->user;
        $team1 = factory(Pool::class)->create();

        $agent2 = factory(Agent::class)->create()->addToTeam($team1);
        $agent3 = factory(Agent::class)->create()->addToTeam($team1);

        $this->be($super);
        $this->visit('helpdesk/admin/teams/1');

        $this->see('<a href="http://localhost/helpdesk/admin/agents/2">' . $agent2->user->name . '</a>')
            ->see('<a href="http://localhost/helpdesk/admin/agents/3">' . $agent3->user->name . '</a>');
    }

    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.team
     * @group acc.admin.team.show
     * @test
     */
    public function it_has_a_list_of_open_tickets()
    {
        $super = factory(Agent::class)->states('isSuper')->create()->user;
        $team1 = factory(Pool::class)->create();

        $ticket1 = factory(Ticket::class)->create()->assignToPool($team1);
        $ticket2 = factory(Ticket::class)->create()->assignToPool($team1);

        $this->be($super);
        $this->visit('helpdesk/admin/teams/1');

        $this->see('<a href="http://localhost/helpdesk/tickets/1">' . $ticket1->content->title . '</a>')
            ->see('<a href="http://localhost/helpdesk/tickets/2">' . $ticket2->content->title . '</a>');
    }
}
