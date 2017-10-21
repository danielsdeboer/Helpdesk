<?php

namespace Aviator\Helpdesk\Tests;

class AdminTeamsUpdateTest extends TestCase
{
    /** @const string */
    const URI = 'helpdesk/admin/teams';

    /** @test */
    public function supervisors_can_update_teams ()
    {
        $team = $this->make->team;
        $this->be($this->make->super->user);

        $this->visitRoute('helpdesk.admin.teams.index');
        $this->patch(self::URI . '/' . $team->id, [
            'name' => 'test team update',
        ]);

        $team = $team->fresh();

        $this->assertResponseStatus(302);
        $this->assertRedirectedToRoute('helpdesk.admin.teams.show', 1);
        $this->assertEquals('test team update', $team->name);
    }

    /** @test */
    public function nonexistent_teams_throw_a_404 ()
    {
        $this->be($this->make->super->user);

        $this->visitRoute('helpdesk.admin.teams.index');
        $this->patch(self::URI . '/99', [
            'name' => 'test team',
        ]);

        $this->assertResponseStatus(404);
    }
}
