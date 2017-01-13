<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Tests\User;
use Illuminate\Support\Facades\Route;

class AdminAgentsDeleteTest extends TestCase
{
    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.agent
     * @group acc.admin.agent.delete
     * @test
     */
    public function guests_cant_visit()
    {
        $this->call('DELETE', 'helpdesk/admin/agents/1');

        $this->assertResponseStatus('302');
        $this->assertRedirectedTo('login');

    }

    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.agent
     * @group acc.admin.agent.delete
     * @test
     */
    public function users_cant_visit()
    {
        $user = factory(User::class)->create();

        $this->be($user);
        $this->call('DELETE', 'helpdesk/admin/agents/1');

        $this->assertResponseStatus('403');
    }

    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.agent
     * @group acc.admin.agent.delete
     * @test
     */
    public function agents_cant_visit()
    {
        $user = factory(Agent::class)->create()->user;

        $this->be($user);
        $this->call('DELETE', 'helpdesk/admin/agents/1');

        $this->assertResponseStatus('403');
    }

    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.agent
     * @group acc.admin.agent.delete
     * @test
     */
    public function supervisors_can_delete()
    {
        $super = factory(Agent::class)->states('isSuper')->create()->user;
        $agent = factory(Agent::class)->create();

        $this->be($super);
        $this->visitRoute('helpdesk.admin.agents.index');
        $response = $this->call('DELETE', 'helpdesk/admin/agents/2', [
            'delete_agent_confirmed' => 1
        ]);

        $this->assertResponseStatus(302);
        $this->assertRedirectedToRoute('helpdesk.admin.agents.index');
    }

    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.agent
     * @group acc.admin.agent.delete
     * @test
     */
    public function the_super_cant_delete_themselves()
    {
        $super = factory(Agent::class)->states('isSuper')->create()->user;

        $this->be($super);
        $this->visitRoute('helpdesk.admin.agents.index');
        $response = $this->call('DELETE', 'helpdesk/admin/agents/1', [
            'delete_agent_confirmed' => 1
        ]);

        $this->assertResponseStatus(404);
    }

    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.agent
     * @group acc.admin.agent.delete
     * @test
     */
    public function a_non_existent_user_cant_be_deleted()
    {
        $super = factory(Agent::class)->states('isSuper')->create()->user;

        $this->be($super);
        $this->visitRoute('helpdesk.admin.agents.index');
        $response = $this->call('DELETE', 'helpdesk/admin/agents/1234', [
            'delete_agent_confirmed' => 1
        ]);

        $this->assertResponseStatus(404);
    }

    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.agent
     * @group acc.admin.agent.delete
     * @test
     */
    public function delete_must_be_confirmed()
    {
        $super = factory(Agent::class)->states('isSuper')->create()->user;

        $this->be($super);
        $this->visitRoute('helpdesk.admin.agents.index');
        $response = $this->call('DELETE', 'helpdesk/admin/agents/1234');

        $this->assertResponseStatus(302);
        $this->assertSessionHasErrors(['delete_agent_confirmed']);
    }
}
