<?php

namespace Aviator\Helpdesk\Tests;

class Bug10Test extends AdminBase
{
    const VERB = 'GET';
    const URIBASE = 'helpdesk/tickets/';
    const URI = 'helpdesk/tickets/1';

    /** @test */
    public function team_assigned_tickets_should_only_show_agents_from_that_team_in_the_assign_select ()
    {
        /*
         * Should see these
         */
        $agent1 = $this->make->agent;
        $agent2 = $this->make->agent;

        /*
         * Should not see these
         */
        $agent3 = $this->make->agent;
        $agent4 = $this->make->agent;

        $team = $this->make->team;
        $ticket = $this->make->ticket->assignToTeam($team);

        $agent1->makeTeamLeadOf($team);
        $agent2->addToTeam($team);

        $this->be($agent1->user);

//        var_dump($team->agents()->with('user')->get());

        $this->visit(self::URI)
            ->assertResponseOk()
            ->see($ticket->content->title())
            ->see('<p class="heading">Assign</p>')
            ->see($this->make->option($agent1, 'agent-option-'))
            ->see($this->make->option($agent2, 'agent-option-'))
            ->dontSee($this->make->option($agent3, 'agent-option-'))
            ->dontSee($this->make->option($agent4, 'agent-option-'));
    }
}
