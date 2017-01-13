<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Tests\AdminBase;

class AdminAgentsStoreTest extends AdminBase
{
    const VERB = 'POST';
    const URI = 'helpdesk/admin/agents';

    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.agent
     * @group acc.admin.agent.store
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
     * @group acc.admin.agent.store
     * @test
     */
    public function supervisors_can_create_agents()
    {
        $super = $this->makeSuper();
        $user = $this->makeUser();

        $this->be($super);
        $this->visitRoute('helpdesk.admin.agents.index');
        $response = $this->call(self::VERB, 'helpdesk/admin/agents', [
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
        $super = $this->makeSuper();
        $agent = $this->makeAgent();

        $this->be($super);

        $this->visitRoute('helpdesk.admin.agents.index');
        $this->call(self::VERB, 'helpdesk/admin/agents', [
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
        $super = $this->makeSuper();

        $this->be($super);
        $this->visitRoute('helpdesk.admin.agents.index');
        $response = $this->call(self::VERB, 'helpdesk/admin/agents', [
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
        $super = $this->makeSuper();
        $user = $this->makeUser();

        $this->be($super);
        $this->visitRoute('helpdesk.admin.agents.index');
        $this->call(self::VERB, 'helpdesk/admin/agents', [
            'user_id' => $user->id
        ]);

        $this->visitRoute('helpdesk.admin.agents.index');
        $this->call('DELETE', 'helpdesk/admin/agents/2', [
            'delete_agent_confirmed' => 1
        ]);

        $this->visitRoute('helpdesk.admin.agents.index');
        $this->call(self::VERB, 'helpdesk/admin/agents', [
            'user_id' => $user->id
        ]);

        $this->assertEquals(3, Agent::withTrashed()->get()->count());
    }
}
