<?php

namespace Aviator\Helpdesk\Tests;

class Bug5Test extends AdminBase
{
    /** @test */
    public function a_team_lead_can_reply_to_any_ticket_assigned_to_their_team ()
    {
        $team = $this->make->team;
        $ticket = $this->make->ticket->assignToTeam($team, null, true);
        $agent = $this->make->agent->makeTeamLeadOf($team);

        $this->assertTrue($team->isTeamLead($agent));

        $this->be($agent->user);

        $this->visit('helpdesk/tickets/' . $ticket->id)
            ->assertResponseOk()
            ->see($ticket->content->title())
            ->see('add reply')
            ->type('test_text', 'reply_body')
            ->press('reply_submit')
            ->assertResponseOk();
    }
}
