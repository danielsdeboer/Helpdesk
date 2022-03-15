<?php

namespace Aviator\Helpdesk\Tests\Feature\Http\Admin\TeamMembers;

use Aviator\Helpdesk\Tests\AdminBase;
use Exception;
use Illuminate\Database\QueryException;

class AddTest extends AdminBase
{
    const VERB = 'POST';
    const URI = 'helpdesk/admin/team-members/add';

    /** @test */
    public function access_test()
    {
        $this->noGuests();
        $this->noUsers();
        $this->noAgents();
    }

    /** @test */
    public function the_request_requires_three_parameters()
    {
        $this->make->agent;
        $this->make->team;
        $super = $this->make->super;

        $this->be($super->user);
        $this->post(self::URI);

        $this->assertValidationFailed(['agent_id', 'team_id', 'from']);
    }

    /** @test */
    public function an_agent_can_be_added_to_a_team()
    {
        $agent = $this->make->agent;
        $team = $this->make->team;
        $super = $this->make->super;

        $this->be($super->user);
        $this->post(self::URI, [
            'agent_id' => $agent->id,
            'team_id' => $team->id,
            'from' => 'agent',
        ]);

        $this->assertEquals(1, $agent->teams->count());
    }

    /** @test */
    public function an_agent_cant_be_added_to_a_team_more_than_once ()
    {
        $agent = $this->make->agent;
        $team = $this->make->team;

        $agent->addToTeam($team);

        try {
            $agent->addToTeam($team);
        } catch (Exception $exception) {
            $this->assertInstanceOf(QueryException::class, $exception);

            return;
        }

        $this->fail('you should not be able to add a user to a team twice');
    }
}
