<?php

namespace Aviator\Helpdesk\Tests\Acceptance;

use Aviator\Helpdesk\Tests\AdminBase;

class AdminAgentsIndexTest extends AdminBase
{
    const VERB = 'get';
    const URI = 'helpdesk/admin';

    /** @test */
    public function access_test()
    {
        $this->noGuests();
        $this->noUsers();
        $this->noAgents();
    }

    /** @test */
    public function supervisors_can_visit()
    {
        $super = $this->make->super;

        $this->be($super->user);
        $this->visit(self::URI);

        $this->see('id="tab-admin-agents"')
            ->see('Add Agent');
    }

    /** @test */
    public function it_has_a_list_of_agents_with_emails_and_teams()
    {
        $super = $this->make->super;
        $team = $this->make->team;
        $agent = $this->make->agent->addToTeam($team);

        $this->be($super->user);
        $this->visit(self::URI);

        $this->see('<a href="http://localhost/helpdesk/admin/agents/' . $agent->user->id . '">')
            ->see('<td class="table-has-va">' . $agent->user->email . '</td>')
            ->see('<a href="http://localhost/helpdesk/admin/teams/1">' . $team->name . '</a>');
    }

    /** @test */
    public function the_agents_list_includes_supervisors ()
    {
        $super = $this->make->super;
        $team = $this->make->team;
        $this->make->agent->addToTeam($team);

        $this->be($super->user);
        $this->visit(self::URI);

        $this->see(
            '<a href="http://localhost/helpdesk/admin/agents/' . $super->id . '">'
        );
    }

    /** @test */
    public function the_user_listing_is_filtered_by_the_user_callback ()
    {
        $this->make->agent;
        $this->make->internalUser;
        $this->make->user;
        $this->make->user;
        $this->make->user;
        $this->make->internalUser;

        $this->beSuper();
        $response = $this->call(self::VERB, 'helpdesk/admin/agents');

        $this->assertEquals(2, count($response->getOriginalContent()->getData()['users']));
    }
}
