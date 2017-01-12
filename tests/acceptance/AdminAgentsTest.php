<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Pool;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Tests\User;
use Illuminate\Support\Facades\Route;

class AdminAgentsTest extends TestCase
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
     * @group acc.admin.agents
     * @test
     */
    public function guests_cant_visit()
    {
        $this->call('GET', 'helpdesk/admin');

        $this->assertResponseStatus('302');
        $this->assertRedirectedTo('login');

    }

    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.agents
     * @test
     */
    public function users_cant_visit()
    {
        $user = factory(User::class)->create();

        $this->be($user);
        $this->call('GET', 'helpdesk/admin');

        $this->assertResponseStatus('403');
    }

    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.agents
     * @test
     */
    public function agents_cant_visit()
    {
        $user = factory(Agent::class)->create()->user;

        $this->be($user);
        $this->call('GET', 'helpdesk/admin');

        $this->assertResponseStatus('403');
    }

    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.agents
     * @test
     */
    public function supervisors_can_visit()
    {
        $user = factory(Agent::class)->states('isSuper')->create()->user;

        $this->be($user);
        $this->visit('helpdesk/admin');

        $this->see('id="tab-admin-agents"')
            ->see('Add Agent');
    }

    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.agents
     * @test
     */
    public function it_has_a_list_of_agents_with_emails_and_teams()
    {
        $user = factory(Agent::class)->states('isSuper')->create()->user;
        $team = factory(Pool::class)->create();
        $agent = factory(Agent::class)->create()->addToTeam($team);

        $this->be($user);
        $this->visit('helpdesk/admin');

        $this->see('<a href="http://localhost/helpdesk/admin/agents/2">' . $agent->user->name . '</a>')
            ->see('<td>' . $agent->user->email . '</td>')
            ->see('<a href="http://localhost/helpdesk/admin/teams/1">' . $team->name . '</a>');
    }

    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.agents
     * @test
     */
    public function the_list_of_agents_does_not_include_the_supervisor()
    {
        $user = factory(Agent::class)->states('isSuper')->create()->user;
        $team = factory(Pool::class)->create();
        $agent = factory(Agent::class)->create()->addToTeam($team);

        $this->be($user);
        $this->visit('helpdesk/admin');

        $this->dontSee('<a href="http://localhost/helpdesk/admin/agents/1">' . $user->name . '</a>');
    }
}