<?php

namespace Aviator\Helpdesk\Tests\Feature\Http;

use Aviator\Helpdesk\Tests\AdminBase;
use PHPUnit\Framework\Attributes\Test;

class AdminAgentsDeleteTest extends AdminBase
{
    const VERB = 'DELETE';

    const URI = 'helpdesk/admin/agents/2';

    #[Test]
    public function access_test()
    {
        $this->noGuests();
        $this->noUsers();
        $this->noAgents();
    }

    #[Test]
    public function supervisors_can_delete()
    {
        $super = $this->make->super;
        $this->make->agent;

        $this->be($super->user);
        $this->visitRoute('helpdesk.admin.agents.index');
        $this->call(self::VERB, 'helpdesk/admin/agents/2', [
            'delete_agent_confirmed' => 1,
        ]);

        $this->assertResponseStatus(302);
        $this->assertRedirectedToRoute('helpdesk.admin.agents.index');
    }

    #[Test]
    public function the_super_cant_delete_themselves()
    {
        $super = $this->make->super;

        $this->be($super->user);
        $this->visitRoute('helpdesk.admin.agents.index');
        $this->call(self::VERB, 'helpdesk/admin/agents/' . $super->id, [
            'delete_agent_confirmed' => 1,
        ]);

        $this->assertResponseStatus(404);
    }

    #[Test]
    public function a_non_existent_user_cant_be_deleted()
    {
        $super = $this->make->super;

        $this->be($super->user);
        $this->visitRoute('helpdesk.admin.agents.index');
        $this->call(self::VERB, 'helpdesk/admin/agents/1234', [
            'delete_agent_confirmed' => 1,
        ]);

        $this->assertResponseStatus(404);
    }

    #[Test]
    public function delete_must_be_confirmed()
    {
        $super = $this->make->super;

        $this->be($super->user);
        $this->visitRoute('helpdesk.admin.agents.index');
        $this->call(self::VERB, 'helpdesk/admin/agents/1234');

        $this->assertResponseStatus(302);
        $this->assertSessionHasErrors(['delete_agent_confirmed']);
    }
}
