<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Pool;
use Aviator\Helpdesk\Tests\User;

class AdminTeamsStoreTest extends TestCase
{
    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.team
     * @group acc.admin.team.store
     * @test
     */
    public function guests_cant_visit()
    {
        $this->call('POST', 'helpdesk/admin/teams');

        $this->assertResponseStatus('302');
        $this->assertRedirectedTo('login');

    }

    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.team
     * @group acc.admin.team.store
     * @test
     */
    public function users_cant_visit()
    {
        $user = factory(User::class)->create();

        $this->be($user);
        $this->call('POST', 'helpdesk/admin/teams');

        $this->assertResponseStatus('403');
    }

    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.team
     * @group acc.admin.team.store
     * @test
     */
    public function agents_cant_visit()
    {
        $user = factory(Agent::class)->create()->user;

        $this->be($user);
        $this->call('POST', 'helpdesk/admin/teams');

        $this->assertResponseStatus('403');
    }

    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.team
     * @group acc.admin.team.store
     * @test
     */
    public function supervisors_can_store_teams()
    {
        $super = factory(Agent::class)->states('isSuper')->create()->user;

        $this->be($super);
        $this->visitRoute('helpdesk.admin.teams.index');
        $response = $this->call('POST', 'helpdesk/admin/teams', [
            'name' => 'test team'
        ]);

        $this->assertResponseStatus(302);
        $this->assertRedirectedToRoute('helpdesk.admin.teams.show', 1);
    }
}
