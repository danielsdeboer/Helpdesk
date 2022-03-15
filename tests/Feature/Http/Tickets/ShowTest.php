<?php

namespace Aviator\Helpdesk\Tests\Feature\Http\Tickets;

use function auth;
use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\GenericContent;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Tests\TestCase;
use function factory;
use Illuminate\Testing\TestResponse;

class ShowTest extends TestCase
{
    private function assertSeeInAssignList (TestResponse $response, Agent $agent)
    {
        $response->assertSee(sprintf(
            '<option value="%s" id="agent-option-%s">%s',
            $agent->user->id,
            $agent->user->id,
            $agent->user->name,
        ), false);
    }

    /** @var string */
    protected $url = 'helpdesk/tickets/';

    /**
     * @param null $id
     * @return string
     */
    protected function url ($id = null): string
    {
        return $this->url . ($id ?: 1);
    }

    /** @test */
    public function guests_may_not_visit ()
    {
        $response = $this->get($this->url());

        $response->assertStatus(302)
            ->assertRedirect('login');
    }

    /** @test */
    public function users_can_only_visit_their_own_tickets ()
    {
        $user = $this->make->user;
        $ticket1 = $this->make->ticket;
        $ticket2 = $this->make->ticket($user);

        $this->be($user);

        $response1 = $this->get($this->url($ticket1->id));
        $response1->assertStatus(404);

        $response2 = $this->get($this->url($ticket2->id));
        $response2->assertStatus(200);
    }

    /** @test */
    public function it_shows_the_header_with_tickets_tab_active ()
    {
        $user = $this->make->user;
        $ticket = $this->make->ticket($user);

        $this->be($user);
        $response = $this->get($this->url($ticket->id));

        $response->assertActiveHeaderTab('tickets');
    }

    /** @test */
    public function it_shows_tickets_status_tags ()
    {
        $user = $this->make->user;
        $ticket = $this->make->ticket($user);

        $this->be($user);

        $response = $this->get($this->url($ticket->id));
        $response->assertSee('id="status-tag-not-assigned"', false);

        $ticket->assignToTeam($this->make->team);

        $response = $this->get($this->url($ticket->id));
        $response->assertSee('id="status-tag-assigned-to-team"', false);

        $ticket->assignToAgent($this->make->agent);

        $response = $this->get($this->url($ticket->id));
        $response->assertSee('id="status-tag-assigned"', false);

        $ticket->close(null, $user);

        $response = $this->get($this->url($ticket->id));
        $response->assertSee('id="status-tag-closed"', false);
    }

    /** @test */
    public function when_a_ticket_is_open_a_user_may_close_or_reply ()
    {
        $user = $this->make->user;
        $ticket = $this->make->ticket($user);

        $this->be($user);

        $response = $this->get($this->url($ticket->id));

        $response->assertSee('id="toolbar-action-close"', false);
        $response->assertSee('id="toolbar-action-reply"', false);
        $response->assertDontSee('id="toolbar-action-open"', false);

        $ticket->close('note', $user);

        $response = $this->get($this->url($ticket->id));

        $response->assertDontSee('id="toolbar-action-close"', false);
        $response->assertDontSee('id="toolbar-action-reply"', false);
        $response->assertSee('id="toolbar-action-open"', false);
    }

    /** @test */
    public function users_dont_see_agent_actions ()
    {
        $adminActions = ['assign', 'note', 'collab'];

        $user = $this->make->user;
        $ticket = $this->make->ticket($user);

        $this->be($user);

        $response = $this->get($this->url($ticket->id));

        foreach ($adminActions as $action) {
            $response->assertDontSee('id="toolbar-action-' . $action . '"');
        }
    }

    /** @test */
    public function agents_dont_see_team_lead_actions ()
    {
        $agentActions = ['reply', 'note', 'close', 'collab'];
        $leadActions = ['assign'];

        $user = $this->make->user;
        $ticket = $this->make->ticket($user);
        $agent = $this->make->agent->assign($ticket);

        $this->be($agent->user);
        $response = $this->get($this->url($ticket->id));

        $response->assertSuccessful();

        foreach ($agentActions as $action) {
            $response->assertSee('id="toolbar-action-' . $action . '"', false);
        }

        foreach ($leadActions as $action) {
            $response->assertDontSee('id="toolbar-action-' . $action . '"', false);
        }
    }

    /** @test */
    public function team_leads_see_all_actions ()
    {
        $openActions = ['reply', 'note', 'close', 'assign'];

        $assignedActions = ['reassign', 'collab'];

        $closedActions = ['open'];

        $user = $this->make->user;
        $ticket = $this->make->ticket($user);
        $agent = $this->make->agent;
        $team = $this->make->team->assign($ticket)->addLead($agent);

        $this->be($agent->user);
        $response = $this->get($this->url($ticket->id));

        $response->assertSuccessful();

        foreach ($openActions as $action) {
            $response->assertSee('id="toolbar-action-' . $action . '"', false);
        }

        $this->make->agent->assign($ticket);
        $response = $this->get($this->url($ticket->id));

        foreach ($assignedActions as $action) {
            $response->assertSee('id="toolbar-action-' . $action . '"', false);
        }

        $ticket->close(null, $agent);
        $response = $this->get($this->url($ticket->id));

        foreach ($closedActions as $action) {
            $response->assertSee('id="toolbar-action-' . $action . '"', false);
        }
    }

    /** @test */
    public function agents_are_listed_alphabetically ()
    {
        $agent1 = $this->make->agentNamed('zzz');
        $agent2 = $this->make->agentNamed('aaa');
        $agent3 = $this->make->agentNamed('ggg');
        $agent4 = $this->make->agentNamed('yyy');

        $ticket = $this->make->ticket;
        $this->make->team->addLead($agent1)->assign($ticket)->addMembers([
            $agent2,
            $agent3,
            $agent4,
        ]);

        $this->be($agent1->user);
        $response = $this->get($this->url($ticket->id));

        $sorted = ['aaa', 'ggg', 'yyy', 'zzz'];
        $count = 0;
        $response->data('agents')->each(function ($item) use ($sorted, &$count) {
            $this->assertSame($sorted[$count], $item->user_name);
            $count++;
        });
    }

    /** @test */
    public function users_dont_see_private_actions ()
    {
        $user = $this->make->user;
        $agent = $this->make->agent;
        $ticket = $this->make->ticket($user)->assignToAgent($agent, null, false);

        $this->be($user);

        $response = $this->get($this->url($ticket->id));

        $response->assertSuccessful();
        $response->assertSee('id="action-opened"', false);
        $response->assertDontSee('id="action-assigned"', false);
    }

    /** @test */
    public function agents_see_private_actions ()
    {
        $user = $this->make->user;
        $agent = $this->make->agent;
        $ticket = $this->make->ticket($user)->assignToAgent($agent, null, false);

        $this->be($agent->user);

        $response = $this->get($this->url($ticket->id));

        $response->assertSuccessful();
        $response->assertSee('id="action-opened"', false);
        $response->assertSee('id="action-assigned"', false);
    }

    /** @test */
    public function supers_can_assign_ticket_when_ticket_is_assigned_to_team ()
    {
        $user = $this->make->user;
        $super = $this->make->super;
        $agent1 = $this->make->agent;
        $agent2 = $this->make->agent;
        $team1 = $this->make->team;
        $team2 = $this->make->team;

        $agent1->addToTeam($team1);
        $agent2->addToTeam($team2);
        $agent1->makeTeamLeadOf($team1);
        $ticket = $this->make->ticket($user)->assignToTeam($team1, null, false);

        $this->be($super->user);
        auth()->user()->is_super = 1;

        $response = $this->get($this->url($ticket->id));

        $response->assertSuccessful();
        $response->assertSee('<p class="heading">Assign</p>', false);
        $response->assertViewHas('agents');
        $response->assertSee('>' . $agent1->user->name, false);
        $response->assertSee('>' . $agent2->user->name, false);
    }

    /** @test */
    public function supers_can_assign_tickets_outside_of_their_own_team ()
    {
        // The super, their team, their fellow users
        $super = $this->make->super;
        $supersTeam = $this->make->team;
        $agentOnSupersTeam = $this->make->agent;

        $agentOnSupersTeam->addToTeam($supersTeam);
        $super->addToTeam($supersTeam);
        $super->makeTeamLeadOf($supersTeam);

        // Everyone else
        $agentOnNoTeam = $this->make->agent;
        $agentOnSomeOtherTeam = $this->make->agent->addToTeam($this->make->team);

        // The ticket assigned to the super's team
        $ticketAssignedToSupersTeam = $this->make->ticket($this->make->user)
            ->assignToTeam($supersTeam);

        $this->be($super->user);

        $response = $this->get(
            $this->url($ticketAssignedToSupersTeam->id)
        );

        $response->assertSuccessful();
        $response->assertSee('<p class="heading">Assign</p>', false);
        $response->assertViewHas('agents');
        $this->assertSeeInAssignList($response, $agentOnSupersTeam);
        $this->assertSeeInAssignList($response, $agentOnNoTeam);
        $this->assertSeeInAssignList($response, $agentOnSomeOtherTeam);
    }

    /** @test */
    public function supers_can_reassign_tickets ()
    {
        $user = $this->make->user;
        $super = $this->make->super;
        $agent = $this->make->agent;
        $ticket = $this->make->ticket($user)->assignToAgent($agent, null, false);

        $this->be($super->user);
        auth()->user()->is_super = 1;

        $response = $this->get($this->url($ticket->id));

        $response->assertSuccessful();
        $response->assertSee('<p class="heading">Reassign</p>', false);
    }

    /** @test */
    public function team_leads_can_reassign_tickets ()
    {
        $user = $this->make->user;
        $agent2 = $this->make->agent;
        $agent = $this->make->agent;
        $team = $this->make->team;
        $ticket = $this->make->ticket($user)->assignToAgent($agent, null, false);

        $agent->addToTeam($team);
        $agent2->addToTeam($team);
        $ticket->assignToTeam($team);
        $agent->makeTeamLeadOf($team);

        $this->be($agent->user);

        $response = $this->get($this->url($ticket->id));

        $response->assertSuccessful();
        $response->assertSee('<p class="heading">Reassign</p>', false);
    }

    /** @test */
    public function can_not_see_open_tickets_in_closed_list ()
    {
        $user = $this->make->user;
        $agent = $this->make->agent;
        $team = $this->make->team;
        $ticket = $this->make->ticket($user)->assignToTeam($team, null, false);

        $agent->addToTeam($team);
        $agent->makeTeamLeadOf($team);

        $this->be($agent->user);

        $response = $this->get('/helpdesk/tickets/');

        $content = $response->getOriginalContent()->getData();

        //Check that closed tickets are being passed and not empty.
        $response->assertViewHas('closed');
        $this->assertSame(0, count($content['closed']));
        $this->assertSame(1, count($content['open']));
    }

    /** @test */
    public function ignored_tickets_are_only_seen_by_supers ()
    {
        $agent = $this->make->agent;
        $super = $this->make->super;
        $team = $this->make->team;
        $user = $this->make->user;
        $ignoredUser = $this->make->user;
        $agent->makeTeamLeadOf($team);

        $ticket = $this->make->ticket($user)->assignToTeam($team, null, false);

        $this->addIgnoredUser([$ignoredUser->email]);

        $ignoredTicket = Ticket::query()->create([
            'user_id' => $ignoredUser->id,
            'content_id' => factory(GenericContent::class)->create()->id,
            'content_type' => 'Aviator\Helpdesk\Models\GenericContent',
            'status' => 'open',
            'uuid' => 2,
        ]);

        //An agent can't see the ignored list.
        $response = $this->actingAs($agent->user)->get('helpdesk/tickets/');
        $response->assertViewHas('ignored');
        $response->assertDontSee('<div class="section" id="ignored">', false);

        //A super can see the ignored list.
        $response = $this->actingAs($super->user)->get('helpdesk/tickets/');
        $response->assertViewHas('ignored');
        $response->assertSee('<div class="section" id="ignored">', false);
    }

    /** @test */
    public function ticket_with_deleted_content (): void
    {
        $agent = $this->make->agent;
        $ticket = $this->make->ticketWithDeletedContent->assignToAgent($agent);

        $this->be($agent->user);

        $this->withoutExceptionHandling();

        $this->call->tickets->show($ticket)
            ->assertOk()
            ->assertSee('Deleted Content');
    }
}
