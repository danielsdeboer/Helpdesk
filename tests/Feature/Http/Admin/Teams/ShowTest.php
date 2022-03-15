<?php

namespace Aviator\Helpdesk\Tests\Feature\Http\Admin\Teams;

use Aviator\Helpdesk\Tests\TestCase;

class ShowTest extends TestCase
{
    /** @test */
    public function it_shows_a_teams_agents (): void
    {
        $team = $this->make->team;
        $team2 = $this->make->team;
        $team3 = $this->make->team;
        $agent = $this->make->super;

        $this->be($agent->user);

        $agent->addToTeam($team2);
        $agent->makeTeamLeadOf($team2);

        $response = $this->get('helpdesk/admin/teams/2');

        $response->assertSeeText('Team Lead');
    }

    /** @test */
    public function handling_deleted_contents (): void
    {
        $this->withoutExceptionHandling();

        $team = $this->make->team;
        $agent = $this->make->super;
        $goodTicket = $this->make->ticket->assignToTeam($team);
        $badTicket = $this->make->ticketWithDeletedContent
            ->assignToTeam($team);

        $this->be($agent->user);

        $agent->addToTeam($team);
        $agent->makeTeamLeadOf($team);

        $this->call->admin->teams->show($team)
            ->assertOk()
            ->assertSee($goodTicket->getSafeContent()->title())
            ->assertSee($badTicket->getSafeContent()->title());
    }
}
