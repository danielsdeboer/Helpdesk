<?php

namespace Aviator\Helpdesk\Tests\Middleware;

use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Tests\TestCase;
use Illuminate\Support\Facades\Route;

class TicketAccessTest extends TestCase
{
    /** @var string */
    protected $url = 'guarded/';

    /**
     * @param int $id
     * @return string
     */
    protected function url (int $id)
    {
        return $this->url . $id;
    }

    /**
     * Create a guarded test route.
     */
    public function setUp ()
    {
        parent::setUp();

        Route::any('/guarded/{ticket}', [
            'middleware' => 'helpdesk.ticket.owner',
            function (Ticket $ticket) {
                return $ticket;
            },
        ]);
    }

    /** @test */
    public function guest_get_a_403 ()
    {
        $response = $this->get($this->url(1));

        $response->assertStatus(403);
    }

    /** @test */
    public function tickets_that_dont_exist_get_a_403 ()
    {
        $this->be($this->make->user);

        $response = $this->get($this->url(99999));

        $response->assertStatus(403);
    }

    /** @test */
    public function it_aborts_if_the_user_doesnt_own_the_ticket ()
    {
        $ticket = $this->make->ticket;

        $this->be($this->make->user);
        $response = $this->get($this->url($ticket->id));

        $response->assertStatus(403);
    }

    /** @test */
    public function it_proceeds_if_the_user_owns_the_ticket ()
    {
        $ticket = $this->make->ticket;

        $this->be($ticket->user);
        $response = $this->get($this->url($ticket->id));

        $response->assertStatus(200);
    }

    /** @test */
    public function it_aborts_if_the_agent_isnt_assigned_to_the_ticket()
    {
        $agent = $this->make->agent;
        $ticket = $this->make->ticket;

        $this->be($agent->user);
        $response = $this->get($this->url($ticket->id));

        $response->assertStatus(403);
    }

    /** @test */
    public function it_proceeds_if_the_agent_is_assigned_to_the_ticket()
    {
        $agent = $this->make->agent;
        $ticket = $this->make->ticket->assignToAgent($agent);

        $this->be($agent->user);
        $response = $this->get($this->url($ticket->id));

        $response->assertStatus(200);
    }

    /** @test */
    public function it_proceeds_if_the_agent_is_a_collaborator ()
    {
        $agent1 = $this->make->agent;
        $agent2 = $this->make->agent;

        $ticket = $this->make->ticket->assignToAgent($agent1)->addCollaborator($agent2, $agent1);

        $this->be($agent2->user);
        $response = $this->get($this->url($ticket->id));

        $response->assertStatus(200);
    }

    /** @test */
    public function it_proceeds_for_team_leads_of_the_assigned_team ()
    {
        $team = $this->make->team;
        $ticket = $this->make->ticket->assignToTeam($team);
        $lead = $this->make->agent->makeTeamLeadOf($team);

        $this->be($lead->user);
        $response = $this->get($this->url($ticket->id));

        $response->assertStatus(200);
    }

    /** @test */
    public function other_team_leads_get_a_403 ()
    {
        $team = $this->make->team;
        $otherTeam = $this->make->team;
        $ticket = $this->make->ticket->assignToTeam($team);

        $lead = $this->make->agent->makeTeamLeadOf($otherTeam);

        $this->be($lead->user);
        $response = $this->get($this->url($ticket->id));

        $response->assertStatus(403);
    }
}
