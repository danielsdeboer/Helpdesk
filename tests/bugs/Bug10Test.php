<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Ticket;

class Bug10Test extends AdminBase
{
    const VERB = 'GET';
    const URIBASE = 'helpdesk/tickets/';
    const URI = 'helpdesk/tickets/1';

    /**
     * @group bugs
     * @group bugs.10
     * @test
     */
    public function whenAssigningAPoolAssignedTicketToAnAgentOnlyAgentsFromThatTeamShouldShowInTheSelect()
    {
        $user = $this->makeUser();
        $agent = $this->makeAgent();
        $team = $this->makeTeam();
        $ticket = factory(Ticket::class)->create([
            'user_id' => $user->id,
        ]);

        $ticket->assignToTeam($team, null, true);
        $agent->makeTeamLeadOf($team);

        $this->be($agent->user);

        $this->visit(self::URI)
            ->assertResponseOk()
            ->see($ticket->content->title())
            ->see('<p class="heading">Assign</p>')
            ->assertResponseOk();
    }
}
