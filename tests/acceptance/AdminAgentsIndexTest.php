<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Tests\AdminBase;

class AdminAgentsIndexTest extends AdminBase
{
    const VERB = 'get';
    const URI = 'helpdesk/admin';

    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.agent
     * @group acc.admin.agent.index
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
     * @group acc.admin.agent.index
     * @test
     */
    public function supervisors_can_visit()
    {
        $super = $this->makeSuper();

        $this->be($super);
        $this->visit(self::URI);

        $this->see('id="tab-admin-agents"')
            ->see('Add Agent');
    }

    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.agent
     * @group acc.admin.agent.index
     * @test
     */
    public function it_has_a_list_of_agents_with_emails_and_teams()
    {
        $super = $this->makeSuper();
        $team = $this->makeTeam();
        $agent = $this->makeAgent()->addToTeam($team);

        $this->be($super);
        $this->visit(self::URI);

        $this->see('<a href="http://localhost/helpdesk/admin/agents/2">' . $agent->user->name . '</a>')
            ->see('<td>' . $agent->user->email . '</td>')
            ->see('<a href="http://localhost/helpdesk/admin/teams/1">' . $team->name . '</a>');
    }

    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.agent
     * @group acc.admin.agent.index
     * @test
     */
    public function the_list_of_agents_does_not_include_the_supervisor()
    {
        $super = $this->makeSuper();
        $team = $this->makeTeam();
        $agent = $this->makeAgent()->addToTeam($team);

        $this->be($super);
        $this->visit(self::URI);

        $this->dontSee('<a href="http://localhost/helpdesk/admin/agents/1">' . $super->name . '</a>');
    }

    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.agent
     * @group acc.admin.agent.index
     * @test
     */
    public function the_user_listing_is_filtered_by_the_user_callback()
    {
        $super = $this->makeSuper();
        $agent = $this->makeAgent();
        $user1 = factory(User::class)->states('isInternal')->create();
        $user2 = $this->makeUser();
        $user3 = $this->makeUser();
        $user4 = $this->makeUser();
        $user5 = factory(User::class)->states('isInternal')->create();

        $this->be($super);
        $response = $this->call(self::VERB, 'helpdesk/admin/agents');

        $this->assertEquals(2, $response->getOriginalContent()->getData()['users']->count());
    }
}