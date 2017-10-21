<?php

namespace Aviator\Helpdesk\Tests;

class AdminAgentsStoreTest extends AdminBase
{
    const VERB = 'POST';
    const URI = 'helpdesk/admin/agents';

    /** @test */
    public function access_test()
    {
        $this->noGuests();
        $this->noUsers();
        $this->noAgents();
    }

    /** @test */
    public function supervisors_can_create_agents ()
    {
        $super = $this->make->super;
        $user = $this->make->user;

        $this->be($super->user);
        $this->visitRoute('helpdesk.admin.agents.index');

        $this->post(self::URI, [
            'user_id' => $user->id,
        ]);

        $this->assertResponseStatus(302);
        $this->assertRedirectedToRoute('helpdesk.admin.agents.show', $user->id);
    }

    /** @test */
    public function the_same_user_cant_be_added_as_an_agent_twice ()
    {
        $super = $this->make->super;
        $agent = $this->make->agent;

        $this->be($super->user);

        $this->visitRoute('helpdesk.admin.agents.index');
        $this->post(self::URI, [
            'user_id' => $agent->user->id,
        ]);

        $this->assertResponseStatus(302);
        $this->assertRedirectedToRoute('helpdesk.admin.agents.index');
        $this->assertSessionHasErrors('user_id');
    }

    /** @test */
    public function a_non_existent_user_cant_be_added ()
    {
        $super = $this->make->super;

        $this->be($super->user);
        $this->visitRoute('helpdesk.admin.agents.index');
        $this->post(self::URI, [
            'user_id' => 999999,
        ]);

        $this->assertResponseStatus(302);
        $this->assertRedirectedToRoute('helpdesk.admin.agents.index');
        $this->assertSessionHasErrors('user_id');
    }

    /** @test */
    public function an_agent_can_be_deleted_and_then_created_again ()
    {
        $super = $this->make->super;
        $user = $this->make->user;

        $this->be($super->user);
        $this->visitRoute('helpdesk.admin.agents.index');
        $this->post(self::URI, [
            'user_id' => $user->id,
        ]);

        $agent = $this->get->latest->agent;

        $this->visitRoute('helpdesk.admin.agents.index');
        $this->delete('helpdesk/admin/agents/' . $agent->id, [
            'delete_agent_confirmed' => 1,
        ]);

        $this->visitRoute('helpdesk.admin.agents.index');
        $this->post(self::URI, [
            'user_id' => $user->id,
        ]);

        $this->assertEquals(4, $this->get->withTrashed->count->agent);
    }
}
