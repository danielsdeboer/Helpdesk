<?php

namespace Aviator\Helpdesk\Tests;

class AdminAgentsDeleteTest extends AdminBase
{
    const VERB = 'DELETE';
    const URI = 'helpdesk/admin/agents/2';

    /**
     * @group acc
     * @group acc.admin
     * @group acc.admin.agent
     * @group acc.admin.agent.delete
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
     * @group acc.admin.agent.delete
     * @test
     */
    public function supervisors_can_delete()
    {
        $super = $this->makeSuper();
        $agent = $this->makeAgent();

        $this->be($super);
        $this->visitRoute('helpdesk.admin.agents.index');
        $response = $this->call(self::VERB, 'helpdesk/admin/agents/2', [
            'delete_agent_confirmed' => 1,
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
        $super = $this->makeSuper();

        $this->be($super);
        $this->visitRoute('helpdesk.admin.agents.index');
        $response = $this->call(self::VERB, 'helpdesk/admin/agents/1', [
            'delete_agent_confirmed' => 1,
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
        $super = $this->makeSuper();

        $this->be($super);
        $this->visitRoute('helpdesk.admin.agents.index');
        $response = $this->call(self::VERB, 'helpdesk/admin/agents/1234', [
            'delete_agent_confirmed' => 1,
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
        $super = $this->makeSuper();

        $this->be($super);
        $this->visitRoute('helpdesk.admin.agents.index');
        $response = $this->call(self::VERB, 'helpdesk/admin/agents/1234');

        $this->assertResponseStatus(302);
        $this->assertSessionHasErrors(['delete_agent_confirmed']);
    }
}
