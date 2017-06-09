<?php

namespace Aviator\Helpdesk\Tests;

class AdminAgentsShowTest extends AdminBase
{
    const VERB = 'GET';
    const URI = 'helpdesk/admin/agents/1';

    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.agent
     * @group acc.admin.agent.show
     * @test
     */
    public function access_test()
    {
        $this->noGuests();
        $this->noUsers();
        $this->noAgents();
    }

    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.agent
     * @group acc.admin.agent.show
     * @test
     */
    public function supervisors_can_visit()
    {
        $super = $this->makeSuper();
        $agent = $this->makeAgent();

        $this->be($super);
        $this->visit('helpdesk/admin/agents/2');

        $this->see('id="tab-admin-agents"')
            ->see('<strong>' . $agent->user->name . '</strong>')
            ->see('<h2 class="subtitle">Added on ' . $agent->created_at->toDateString() . '</h2>')
            ->see('In 0 teams')
            ->see('0 open tickets');
    }

    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.agent
     * @group acc.admin.agent.show
     * @test
     */
    public function it_has_a_list_of_the_agents_teams()
    {
        $super = $this->makeSuper();
        $team = $this->makeTeam();
        $team2 = $this->makeTeam();
        $agent = $this->makeAgent()->addToTeams([$team, $team2]);

        $this->be($super);
        $this->visit('helpdesk/admin/agents/2');

        $this->see('<a href="http://localhost/helpdesk/admin/teams/1">' . $team->name . '</a>')
            ->see('<a href="http://localhost/helpdesk/admin/teams/2">' . $team2->name . '</a>');
    }

    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.agent
     * @group acc.admin.agent.show
     * @test
     */
    public function it_has_a_list_of_the_agents_open_tickets()
    {
        $super = $this->makeSuper();
        $agent = $this->makeAgent();
        $agent2 = $this->makeAgent();

        $ticket1 = $this->makeTicket()->assignToAgent($agent);
        $ticket2 = $this->makeTicket()->assignToAgent($agent);
        $ticket3 = $this->makeTicket()->assignToAgent($agent2);

        $this->be($super);
        $this->visit('helpdesk/admin/agents/2');

        $this->see('<a href="http://localhost/helpdesk/tickets/1">' . $ticket1->content->title . '</a>')
            ->see('<a href="http://localhost/helpdesk/tickets/2">' . $ticket2->content->title . '</a>')
            ->dontSee('<a href="http://localhost/helpdesk/tickets/3">' . $ticket3->content->title . '</a>');
    }
}
