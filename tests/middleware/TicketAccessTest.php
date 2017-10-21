<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Ticket;
use Illuminate\Support\Facades\Route;

class TicketAccessTest extends TestCase
{
    /** @const string */
    const URI = 'guarded/';

    /**
     * Setup.
     */
    public function setUp()
    {
        parent::setUp();

        Route::any(
            '/guarded/{ticket}',
            [
                'middleware' => 'helpdesk.ticket.owner',
                function (Ticket $ticket) {
                    return $ticket;
                }
            ]
        );
    }

    /** @test */
    public function guest_get_a_403 ()
    {
        $this->get(self::URI . '1');

        $this->assertResponseStatus(403);
    }

    /** @test */
    public function it_aborts_if_the_ticket_isnt_found()
    {
        $this->be($this->make->user);

        $this->get(self::URI . '1');

        $this->assertResponseStatus(403);
    }

    /** @test */
    public function it_aborts_if_the_user_doesnt_own_the_ticket()
    {
        $ticket = $this->make->ticket;
        $notOwner = $this->make->user;

        $this->be($notOwner);
        $this->get(self::URI . $ticket->id);

        $this->assertResponseStatus(403);
    }

    /** @test */
    public function it_proceeds_if_the_user_owns_the_ticket()
    {
        $ticket = $this->make->ticket;

        $this->be($ticket->user);
        $this->get(self::URI . $ticket->id);

        $this->assertResponseOk();
    }

    /** @test */
    public function it_aborts_if_the_agent_isnt_assigned_to_the_ticket()
    {
        $agent = $this->make->agent;
        $ticket = $this->make->ticket;

        $this->be($agent->user);
        $this->get(self::URI . $ticket->id);

        $this->assertResponseStatus(403);
    }

    /** @test */
    public function it_proceeds_if_the_agent_is_assigned_to_the_ticket()
    {
        $agent = $this->make->agent;
        $ticket = $this->make->ticket->assignToAgent($agent);

        $this->be($agent->user);
        $this->get(self::URI . $ticket->id);

        $this->assertResponseOk();
    }

    /** @test */
    public function it_proceeds_if_the_agent_is_a_collaborator()
    {
        $agent1 = $this->make->agent;
        $agent2 = $this->make->agent;

        $ticket = $this->make->ticket->assignToAgent($agent1)->addCollaborator($agent2, $agent1);

        $this->be($agent2->user);
        $this->get(self::URI . $ticket->id);

        $this->assertResponseOk();
    }

    /** @test */
    public function team_leads_may_view_if_they_are_lead_of_the_tickets_assigned_team ()
    {
        $team = $this->make->team;
        $ticket = $this->make->ticket->assignToTeam($team);
        $lead = $this->make->agent->makeTeamLeadOf($team);

        $this->be($lead->user);
        $this->get(self::URI . $ticket->id);

        $this->assertResponseOk();
    }

    /** @test */
    public function other_team_leads_may_not_view ()
    {
        $team = $this->make->team;
        $otherTeam = $this->make->team;
        $ticket = $this->make->ticket->assignToTeam($team);

        $lead = $this->make->agent->makeTeamLeadOf($otherTeam);

        $this->be($lead->user);
        $this->get(self::URI . $ticket->id);

        $this->assertResponseStatus(403);
    }
}
