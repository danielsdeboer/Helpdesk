<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Pool;
use Aviator\Helpdesk\Models\Agent;

class AdminTeamsDeleteTest extends TestCase
{
    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.team
     * @group acc.admin.team.delete
     * @test
     */
    public function supervisors_can_delete_teams()
    {
        $super = factory(Agent::class)->states('isSuper')->create()->user;
        $team = factory(Pool::class)->create();

        $this->be($super);
        $this->visitRoute('helpdesk.admin.teams.index');
        $response = $this->call('DELETE', 'helpdesk/admin/teams/1', [
            'delete_team_confirmed' => 1,
        ]);

        $this->assertResponseStatus(302);
        $this->assertRedirectedToRoute('helpdesk.admin.teams.index');
        $this->assertEquals(0, Pool::all()->count());
        $this->assertEquals(1, Pool::withTrashed()->get()->count());
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
        $response = $this->call('DELETE', 'helpdesk/admin/teams/1', [
            'delete_team_confirmed' => 1,
        ]);

        $this->assertResponseStatus(404);
    }

    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.team
     * @group acc.admin.team.update
     * @test
     */
    public function delete_confirmation_is_required()
    {
        $super = factory(Agent::class)->states('isSuper')->create()->user;
        $team = factory(Pool::class)->create();

        $this->be($super);
        $this->visitRoute('helpdesk.admin.teams.index');
        $response = $this->call('DELETE', 'helpdesk/admin/teams/1', [
            'delete_team_confirmed' => 0,
        ]);

        $this->assertResponseStatus(302);
        $this->assertRedirectedToRoute('helpdesk.admin.teams.index');
        $this->assertSessionHasErrors('delete_team_confirmed');
    }
}
