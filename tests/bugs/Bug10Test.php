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
        $agent1 = $this->makeAgent();
        $agent2 = $this->makeAgent();
        $agent3 = $this->makeAgent();
        $agent4 = $this->makeAgent();
        $team = $this->makeTeam();
        $ticket = factory(Ticket::class)->create([
            'user_id' => $user->id,
        ]);

        $ticket->assignToTeam($team, null, true);
        $agent1->makeTeamLeadOf($team);
        $agent2->addToTeam($team);

        $this->be($agent1->user);

        $this->visit(self::URI)
            ->assertResponseOk()
            ->see($ticket->content->title())
            ->see('<p class="heading">Assign</p>')
            ->see($agent1->user->name)
            ->see($agent2->user->name)
            ->dontSee($agent3->user->name)
            ->dontSee($agent4->user->name)
            ->assertResponseOk();
    }
}
