<?php

namespace Aviator\Helpdesk\Tests;

class AdminTeamsDeleteTest extends TestCase
{
    /** @const string */
    const URI = 'helpdesk/admin/teams';

    /** @test */
    public function supervisors_can_delete_teams ()
    {
        $super = $this->make->super;
        $team = $this->make->team;

        $this->be($super->user);
        $this->visitRoute('helpdesk.admin.teams.index');
        $this->delete(self::URI . '/' . $team->id, [
            'delete_team_confirmed' => 1,
        ]);

        $this->assertResponseStatus(302);
        $this->assertRedirectedToRoute('helpdesk.admin.teams.index');
        $this->assertEquals(0, $this->get->count->team);
        $this->assertEquals(1, $this->get->withTrashed->count->team);
    }

    /** @test */
    public function nonexistent_teams_throw_a_404 ()
    {
        $super = $this->make->super;

        $this->be($super->user);
        $this->delete(self::URI . '/99', [
            'delete_team_confirmed' => 1,
        ]);

        $this->assertResponseStatus(404);
    }

    /** @test */
    public function delete_confirmation_is_required()
    {
        $super = $this->make->super;
        $team = $this->make->team;

        $this->be($super->user);
        $this->visitRoute('helpdesk.admin.teams.index');
        $this->delete(self::URI . '/' . $team->id, [
            'delete_team_confirmed' => 0,
        ]);

        $this->assertResponseStatus(302);
        $this->assertRedirectedToRoute('helpdesk.admin.teams.index');
        $this->assertSessionHasErrors('delete_team_confirmed');
    }
}
