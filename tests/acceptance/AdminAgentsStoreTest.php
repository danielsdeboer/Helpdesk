<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Tests\User;
use Illuminate\Support\Facades\Route;

class AdminAgentsStoreTest extends TestCase
{
    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.agent
     * @group acc.admin.agent.store
     * @test
     */
    public function guests_cant_visit()
    {
        $this->call('POST', 'helpdesk/admin/agents');

        $this->assertResponseStatus('302');
        $this->assertRedirectedTo('login');

    }

    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.agent
     * @group acc.admin.agent.store
     * @test
     */
    public function users_cant_visit()
    {
        $user = factory(User::class)->create();

        $this->be($user);
        $this->call('POST', 'helpdesk/admin/agents');

        $this->assertResponseStatus('403');
    }

    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.agent
     * @group acc.admin.agent.store
     * @test
     */
    public function agents_cant_visit()
    {
        $user = factory(Agent::class)->create()->user;

        $this->be($user);
        $this->call('POST', 'helpdesk/admin/agents');

        $this->assertResponseStatus('403');
    }

    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.agent
     * @group acc.admin.agent.store
     * @test
     */
    public function supervisors_can_create_users()
    {
        $super = factory(Agent::class)->states('isSuper')->create()->user;
        $user = factory(User::class)->create();

        $this->be($super);

        $this->visitRoute('helpdesk.admin.agents.index');
        $response = $this->call('POST', 'helpdesk/admin/agents', [
            'user_id' => $user->id
        ]);

        $this->assertResponseStatus(302);
        $this->assertRedirectedToRoute('helpdesk.admin.agents.show', 2);
    }

    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.agent
     * @group acc.admin.agent.store
     * @test
     */
    public function the_same_user_cant_be_added_as_an_agent_twice()
    {
        $super = factory(Agent::class)->states('isSuper')->create()->user;
        $agent = factory(Agent::class)->create();

        $this->be($super);

        $this->visitRoute('helpdesk.admin.agents.index');
        $response = $this->call('POST', 'helpdesk/admin/agents', [
            'user_id' => $agent->user->id
        ]);

        $this->assertResponseStatus(302);
        $this->assertRedirectedToRoute('helpdesk.admin.agents.index');
        $this->assertSessionHasErrors('user_id');
    }

    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.agent
     * @group acc.admin.agent.store
     * @test
     */
    public function a_non_existent_user_cant_be_added()
    {
        $super = factory(Agent::class)->states('isSuper')->create()->user;

        $this->be($super);
        $this->visitRoute('helpdesk.admin.agents.index');
        $response = $this->call('POST', 'helpdesk/admin/agents', [
            'user_id' => 999999
        ]);

        $this->assertResponseStatus(302);
        $this->assertRedirectedToRoute('helpdesk.admin.agents.index');
        $this->assertSessionHasErrors('user_id');
    }

    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.agent
     * @group acc.admin.agent.store
     * @test
     */
    public function an_agent_can_be_deleted_and_then_created_again()
    {
        $super = factory(Agent::class)->states('isSuper')->create()->user;
        $user = factory(User::class)->create();

        $this->be($super);
        $this->visitRoute('helpdesk.admin.agents.index');
        $this->call('POST', 'helpdesk/admin/agents', [
            'user_id' => $user->id
        ]);

        $this->visitRoute('helpdesk.admin.agents.index');
        $this->call('DELETE', 'helpdesk/admin/agents/2', [
            'delete_agent_confirmed' => 1
        ]);

        $this->visitRoute('helpdesk.admin.agents.index');
        $this->call('POST', 'helpdesk/admin/agents', [
            'user_id' => $user->id
        ]);

        $this->assertEquals(3, Agent::withTrashed()->get()->count());
    }
}
