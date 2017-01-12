<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Pool;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Tests\User;
use Illuminate\Support\Facades\Route;

class AdminTeamsIndexTest extends TestCase
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
     * @group acc.admin.team
     * @group acc.admin.team.index
     * @test
     */
    public function guests_cant_visit()
    {
        $this->call('GET', 'helpdesk/admin/teams');

        $this->assertResponseStatus('302');
        $this->assertRedirectedTo('login');

    }

    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.team
     * @group acc.admin.team.index
     * @test
     */
    public function users_cant_visit()
    {
        $user = factory(User::class)->create();

        $this->be($user);
        $this->call('GET', 'helpdesk/admin/teams');

        $this->assertResponseStatus('403');
    }

    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.team
     * @group acc.admin.team.index
     * @test
     */
    public function agents_cant_visit()
    {
        $user = factory(Agent::class)->create()->user;

        $this->be($user);
        $this->call('GET', 'helpdesk/admin/teams');

        $this->assertResponseStatus('403');
    }

    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.team
     * @group acc.admin.team.index
     * @test
     */
    public function supervisors_can_visit()
    {
        $user = factory(Agent::class)->states('isSuper')->create()->user;
        $team = factory(Pool::class)->create();

        $this->be($user);
        $this->visit('helpdesk/admin/teams');

        $this->see('id="tab-admin-agents"')
            ->see('<a href="http://localhost/helpdesk/admin/teams/1">' . $team->name . '</a>');
        }

    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.team
     * @group acc.admin.team.index
     * @test
     */
    public function it_has_a_list_of_all_teams()
    {
        $user = factory(Agent::class)->states('isSuper')->create()->user;
        $team1 = factory(Pool::class)->create();
        $team2 = factory(Pool::class)->create();

        $this->be($user);
        $this->visit('helpdesk/admin/teams');

        $this->see('<a href="http://localhost/helpdesk/admin/teams/1">' . $team1->name . '</a>')
            ->see('<a href="http://localhost/helpdesk/admin/teams/2">' . $team2->name . '</a>');
    }

    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.team
     * @group acc.admin.team.index
     * @test
     */
    public function it_lists_agents_in_teams()
    {
        $super = factory(Agent::class)->states('isSuper')->create()->user;
        $team = factory(Pool::class)->create();

        $agent2 = factory(Agent::class)->create()->addToTeam($team);
        $agent3 = factory(Agent::class)->create()->addToTeam($team);

        $this->be($super);
        $this->visit('helpdesk/admin/teams');

        $this->see('<a href="http://localhost/helpdesk/admin/agents/2">' . $agent2->user->name . '</a>')
            ->see('<a href="http://localhost/helpdesk/admin/agents/3">' . $agent3->user->name . '</a>');
    }

}