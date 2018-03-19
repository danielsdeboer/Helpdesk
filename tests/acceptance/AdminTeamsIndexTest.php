<?php

namespace Aviator\Helpdesk\Tests;

use Illuminate\Support\Facades\Route;

class AdminTeamsIndexTest extends BKTestCase
{
    const URI = 'helpdesk/admin/teams';

    public function setUp()
    {
        parent::setUp();

        Route::any('login', function () {
        });
    }

    /** @test */
    public function guests_cant_visit ()
    {
        $this->get(self::URI);

        $this->assertResponseStatus('302');
        $this->assertRedirectedTo('login');
    }

    /** @test */
    public function users_cant_visit ()
    {
        $this->be($this->make->user);
        $this->get(self::URI);

        $this->assertResponseStatus('403');
    }

    /** @test */
    public function agents_cant_visit ()
    {
        $this->be($this->make->agent->user);
        $this->get(self::URI);

        $this->assertResponseStatus('403');
    }

    /** @test */
    public function supervisors_can_visit ()
    {
        $team = $this->make->team;

        $this->be($this->make->super->user);
        $this->visit(self::URI);

        $this->see('id="tab-admin-agents"')
            ->see('<a href="http://localhost/helpdesk/admin/teams/1">' . $team->name . '</a>');
    }

    /** @test */
    public function it_has_a_list_of_all_teams ()
    {
        $super = $this->make->super;
        $team1 = $this->make->team;
        $team2 = $this->make->team;

        $this->be($super->user);
        $this->visit(self::URI);

        $this->see('<a href="http://localhost/helpdesk/admin/teams/1">' . $team1->name . '</a>')
            ->see('<a href="http://localhost/helpdesk/admin/teams/2">' . $team2->name . '</a>');
    }

    /** @test */
    public function it_lists_agents_in_teams ()
    {
        $super = $this->make->super;
        $team = $this->make->team;

        $agent2 = $this->make->agent->addToTeam($team);
        $agent3 = $this->make->agent->addToTeam($team);

        $this->be($super->user);
        $this->visit(self::URI);

        $this->see('<a href="http://localhost/helpdesk/admin/agents/' . $agent2->id . '">' . $agent2->user->name . '</a>')
            ->see('<a href="http://localhost/helpdesk/admin/agents/' . $agent3->id . '">' . $agent3->user->name . '</a>');
    }
}
