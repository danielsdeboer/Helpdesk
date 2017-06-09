<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Pool;
use Aviator\Helpdesk\Models\Agent;

class AdminTeamsUpdateTest extends TestCase
{
    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.team
     * @group acc.admin.team.update
     * @test
     */
    public function supervisors_can_update_teams()
    {
        $super = factory(Agent::class)->states('isSuper')->create()->user;
        $team = factory(Pool::class)->create();

        $this->be($super);
        $this->visitRoute('helpdesk.admin.teams.index');
        $response = $this->call('PATCH', 'helpdesk/admin/teams/1', [
            'name' => 'test team update',
        ]);

        $team = $team->fresh();

        $this->assertResponseStatus(302);
        $this->assertRedirectedToRoute('helpdesk.admin.teams.show', 1);
        $this->assertEquals('test team update', $team->name);
    }

    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.team
     * @group acc.admin.team.update
     * @test
     */
    public function nonexistent_teams_throw_a_404()
    {
        $super = factory(Agent::class)->states('isSuper')->create()->user;

        $this->be($super);
        $this->visitRoute('helpdesk.admin.teams.index');
        $response = $this->call('PATCH', 'helpdesk/admin/teams/1', [
            'name' => 'test team',
        ]);

        $this->assertResponseStatus(404);
    }
}
