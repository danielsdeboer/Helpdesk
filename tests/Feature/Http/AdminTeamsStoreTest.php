<?php

namespace Aviator\Helpdesk\Tests\Feature\Http;

use Aviator\Helpdesk\Tests\BKTestCase;
use PHPUnit\Framework\Attributes\Test;

class AdminTeamsStoreTest extends BKTestCase
{
    /** @const string */
    const URI = 'helpdesk/admin/teams';

    #[Test]
    public function guests_cant_visit()
    {
        $this->post('helpdesk/admin/teams');

        $this->assertResponseStatus(302);
        $this->assertRedirectedTo('login');
    }

    #[Test]
    public function users_cant_visit()
    {
        $this->be($this->make->user);
        $this->post(self::URI);

        $this->assertResponseStatus(403);
    }

    #[Test]
    public function agents_cant_visit()
    {
        $this->be($this->make->agent->user);
        $this->post(self::URI);

        $this->assertResponseStatus(403);
    }

    #[Test]
    public function supervisors_can_store_teams()
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
