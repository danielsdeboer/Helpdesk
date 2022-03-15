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

        /** @var \Aviator\Helpdesk\Models\Agent $agent */
        foreach ([$agent2, $agent3] as $agent) {
            $this->see(sprintf(
                'href="http://localhost/helpdesk/admin/agents/%s"',
                $agent->getKey(),
            ));

            $this->see(sprintf(
                'data-agent-id="%s"',
                $agent->getKey(),
            ));

            $this->see(sprintf(
                'data-agent-name="%s"',
                $agent->user->name,
            ));
        }
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

        $this->see('data-section="tickets-table"');

        /** @var \Aviator\Helpdesk\Models\Ticket $ticket */
        foreach ([$ticket1, $ticket2] as $ticket) {
            $this->see(sprintf(
                'href="http://localhost/helpdesk/tickets/%s"',
                $ticket->getKey(),
            ));

            $this->see(sprintf(
                'data-ticket-id="%s"',
                $ticket->getKey(),
            ));

            $this->see(sprintf(
                'data-ticket-title="%s"',
                $ticket->getSafeContent()->title(),
            ));
        }
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
