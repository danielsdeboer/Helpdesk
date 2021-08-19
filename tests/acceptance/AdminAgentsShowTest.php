<?php

namespace Aviator\Helpdesk\Tests;

class AdminAgentsShowTest extends AdminBase
{
    const VERB = 'GET';
    const URI = 'helpdesk/admin/agents/1';

    /** @test */
    public function access_test()
    {
        $this->noGuests();
        $this->noUsers();
        $this->noAgents();
    }

    /** @test */
    public function supervisors_can_visit ()
    {
        $super = $this->make->super;
        $agent = $this->make->agent;

        $this->be($super->user);
        $this->visit('helpdesk/admin/agents/' . $agent->id);

        $this->see('id="tab-admin-agents"')
            ->see('<h2 class="subtitle">Added on ' . $agent->created_at->toDateString() . '</h2>')
            ->see('In 0 teams')
            ->see('0 open tickets');
    }

    /** @test */
    public function it_lists_the_teams_the_agent_belongs_to ()
    {
        $super = $this->make->super;
        $team = $this->make->team;
        $team2 = $this->make->team;
        $agent = $this->make->agent->addToTeams([$team, $team2]);

        $this->be($super->user);
        $this->visit('helpdesk/admin/agents/' . $agent->id);

        $this->see('<a href="http://localhost/helpdesk/admin/teams/1">' . $team->name . '</a>')
            ->see('<a href="http://localhost/helpdesk/admin/teams/2">' . $team2->name . '</a>');
    }

    /** @test */
    public function it_has_a_list_of_the_agents_open_tickets ()
    {
        $super = $this->make->super;
        $agent = $this->make->agent;
        $agent2 = $this->make->agent;

        $ticket1 = $this->make->ticket->assignToAgent($agent);
        $ticket2 = $this->make->ticket->assignToAgent($agent);
        $ticket3 = $this->make->ticket->assignToAgent($agent2);

        $this->be($super->user);
        $this->visit('helpdesk/admin/agents/' . $agent->id);

        $this->see('<a href="http://localhost/helpdesk/tickets/1">' . $ticket1->content->title() . '</a>')
            ->see('<a href="http://localhost/helpdesk/tickets/2">' . $ticket2->content->title() . '</a>')
            ->dontSee('<a href="http://localhost/helpdesk/tickets/3">' . $ticket3->content->title() . '</a>');
    }
}
