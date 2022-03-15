<?php

namespace Aviator\Helpdesk\Tests\Feature\Http;

use Aviator\Helpdesk\Tests\BKTestCase;
use Illuminate\Support\Facades\Route;

class AdminTeamsShowTest extends BKTestCase
{
    /** @const string */
    const URI = 'helpdesk/admin/teams/1';

    public function setUp (): void
    {
        parent::setUp();

        Route::any('login', function () {
        });
    }

    /** @test */
    public function guests_cant_visit ()
    {
        $this->get(self::URI);

        $this->assertResponseStatus(302);
        $this->assertRedirectedTo('login');
    }

    /** @test */
    public function users_cant_visit ()
    {
        $this->be($this->make->user);
        $this->get(self::URI);

        $this->assertResponseStatus(403);
    }

    /** @test */
    public function agents_cant_visit ()
    {
        $this->be($this->make->agent->user);
        $this->get(self::URI);

        $this->assertResponseStatus(403);
    }

    /** @test */
    public function supervisors_can_visit ()
    {
        $super = $this->make->super;
        $team = $this->make->team;

        $this->be($super->user);
        $this->visit(self::URI);

        $this->see('id="tab-admin-agents"')
            ->see('<strong>' . $team->name . '</strong>')
            ->see('0 open tickets');
    }

    /** @test */
    public function it_has_a_list_of_agents ()
    {
        $super = $this->make->super;
        $team1 = $this->make->team;

        $agent2 = $this->make->agent->addToTeam($team1);
        $agent3 = $this->make->agent->addToTeam($team1);

        $this->be($super->user);
        $this->visit(self::URI);

        $this->see('<a href="http://localhost/helpdesk/admin/agents/' . $agent2->id . '">')
            ->see('<a href="http://localhost/helpdesk/admin/agents/' . $agent3->id . '">');
    }

    /** @test */
    public function it_has_a_list_of_open_tickets ()
    {
        $super = $this->make->super;
        $team1 = $this->make->team;

        $ticket1 = $this->make->ticket->assignToTeam($team1);
        $ticket2 = $this->make->ticket->assignToTeam($team1);

        $this->be($super->user);
        $this->visit(self::URI);

        $this->see('<a href="http://localhost/helpdesk/tickets/' . $ticket1->id . '">' . $ticket1->content->title() . '</a>')
            ->see('<a href="http://localhost/helpdesk/tickets/' . $ticket2->id . '">' . $ticket2->content->title() . '</a>');
    }

    /** @test */
    public function it_displays_nothing_to_see_here_on_no_tickets ()
    {
        $super = $this->make->super;
        $team1 = $this->make->team;

        $this->be($super->user);
        $this->visit(self::URI);

        $this->see('<p>Nothing to see here!</p>');
    }
}
