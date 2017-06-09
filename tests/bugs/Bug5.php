<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Pool;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Tests\AdminBase;

class Bug5 extends AdminBase
{
    const VERB = 'GET';
    const URIBASE = 'helpdesk/tickets/';
    const URI = 'helpdesk/tickets/1';

    /**
     * @group bugs
     * @group bugs.5
     * @test
     */
    public function aTeamLeadCanReplyToATicketAssignedToTheirTeam()
    {
        $user = $this->makeUser();
        $agent = $this->makeAgent();
        $team = $this->makeTeam();
        $ticket = factory(Ticket::class)->create([
            'user_id' => $user->id
        ]);

        $ticket->assignToTeam($team, null, true);
        $agent->makeTeamLeadOf($team);

        $this->assertTrue($team->isTeamLead($agent));

        $this->be($agent->user);

        $this->visit(self::URI)
            ->assertResponseOk()
            ->see($ticket->content->title())
            ->see('add reply')
            ->type('test_text', 'reply_body')
            ->press('reply_submit')
            ->assertResponseOk();
    }
}
