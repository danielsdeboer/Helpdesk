<?php

namespace Aviator\Helpdesk\Tests;

class AdminTeamsStoreTest extends TestCase
{
    /** @const string */
    const URI = 'helpdesk/admin/teams';

    /** @test */
    public function guests_cant_visit ()
    {
        $this->post('helpdesk/admin/teams');

        $this->assertResponseStatus('302');
        $this->assertRedirectedTo('login');
    }

    /** @test */
    public function users_cant_visit ()
    {
        $this->be($this->make->user);
        $this->post(self::URI);

        $this->assertResponseStatus('403');
    }

    /** @test */
    public function agents_cant_visit ()
    {
        $this->be($this->make->agent->user);
        $this->post(self::URI);

        $this->assertResponseStatus('403');
    }

    /** @test */
    public function supervisors_can_store_teams ()
    {
        $this->be($this->make->super->user);

        $this->visitRoute('helpdesk.admin.teams.index');
        $this->post(self::URI, [
            'name' => 'test team',
        ]);

        $this->assertResponseStatus(302);
        $this->assertRedirectedToRoute('helpdesk.admin.teams.show', 1);
    }
}
