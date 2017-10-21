<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Team;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Repositories\Tickets;

class TicketsTest extends TestCase
{
    /** @test */
    public function the_foruser_static_returns_an_instance_with_the_user_set()
    {
        $user = $this->make->user;

        $tickets = Tickets::forUser($user);

        $this->assertInstanceOf(Tickets::class, $tickets);
        $this->assertSame($user, $tickets->getUser());
    }

    /** @test */
    public function the_foragent_static_returns_an_instance_with_the_agent_and_user_set()
    {
        $agent = $this->make->agent;

        $tickets = Tickets::forAgent($agent);

        $this->assertInstanceOf(Tickets::class, $tickets);
        $this->assertSame($agent->user, $tickets->getUser());
        $this->assertSame($agent, $tickets->getAgent());
    }

    /** @test */
    public function the_forsuper_static_return_an_instance_with_agent_user_and_super_set ()
    {
        $super = $this->make->super;

        $tickets = Tickets::forSuper($super);

        $this->assertInstanceOf(Tickets::class, $tickets);
        $this->assertSame($super->user, $tickets->getUser());
        $this->assertSame($super, $tickets->getAgent());
        $this->assertTrue($tickets->getSuper());
    }

    /** @test */
    public function the_forsuper_static_only_sets_super_to_true_if_the_agent_is_the_supervisor()
    {
        $super = $this->make->super;
        $notSuper = $this->make->agent;

        $superTickets = Tickets::forSuper($super);
        $notSuperTickets = Tickets::forSuper($notSuper);

        $this->assertTrue($superTickets->getSuper());
        $this->assertNull($notSuperTickets->getSuper());
    }

    /** @test */
    public function the_all_method_returns_only_open_tickets_owned_by_the_user()
    {
        $user1 = $this->make->user;
        $user2 = $this->make->user;
        $user1Tickets = $this->make->tickets(10, $user1);
        $this->make->tickets(10, $user2);

        $user1Tickets = Ticket::with('user')->whereIn('id', $user1Tickets->pluck('id'))->get();
        $tickets = Tickets::forUser($user1)->all();

        $this->assertEquals(10, $tickets->count());
        $this->assertEquals($user1Tickets->toArray(), $tickets->toArray());
    }

    /** @test */
    public function the_all_method_returns_only_open_tickets_assigned_to_the_agent()
    {
        $agent1 = $this->make->agent;
        $agent2 = $this->make->agent;

        $this->make->tickets(5)
            ->each(function (Ticket $ticket) use ($agent1) {
                $ticket->assignToAgent($agent1);
            });

        $this->make->tickets(6)
            ->each(function (Ticket $ticket) use ($agent2) {
                $ticket->assignToAgent($agent2);
            });

        $tickets = Tickets::forAgent($agent1)->all();

        $this->assertEquals(5, $tickets->count());
    }

    /** @test */
    public function the_all_method_returns_all_open_tickets_in_supervisor_context()
    {
        $super = $this->make->super;
        $agent1 = $this->make->agent;
        $agent2 = $this->make->agent;

        $this->make->tickets(5)
             ->each(function (Ticket $ticket) use ($agent1) {
                 $ticket->assignToAgent($agent1);
             });

         $this->make->tickets(6)
             ->each(function (Ticket $ticket) use ($agent2) {
                 $ticket->assignToAgent($agent2);
             });

        $tickets = Tickets::forSuper($super)->all();

        $this->assertEquals(11, $tickets->count());
    }

    /** @test */
    public function the_overdue_method_returns_only_overdue_tickets_owned_by_the_user()
    {
        $user1 = $this->make->user;
        $user2 = $this->make->user;

        /*
         * Overdue for user 1
         */
        $this->make->tickets(5, $user1)
            ->each(function (Ticket $ticket) {
                $ticket->dueOn('yesterday');
            });

        /*
         * On time for user 1
         */
        $this->make->tickets(4, $user1)
            ->each(function (Ticket $ticket) {
                $ticket->dueOn('tomorrow');
            });

        /*
         * Overdue for user 2
         */
        $this->make->tickets(9, $user2)
            ->each(function (Ticket $ticket) {
                $ticket->dueOn('yesterday');
            });

        /*
         * On time for user 2
         */
        $this->make->tickets(10, $user2)
            ->each(function (Ticket $ticket) {
                $ticket->dueOn('tomorrow');
            });

        $overdueTickets = Ticket::with('user', 'dueDate')
            ->where('user_id', $user1->id)
            ->overdue()
            ->get();

        $tickets = Tickets::forUser($user1)->overdue();

        $this->assertEquals(5, $tickets->count());
        $this->assertEquals($overdueTickets->toArray(), $tickets->toArray());
    }

    /** @test */
    public function the_overdue_method_returns_only_overdue_tickets_assigned_to_the_agent()
    {
        $agent1 = $this->make->agent;
        $agent2 = $this->make->agent;

        /*
         * Overdue for agent 1
         */
        $this->make->tickets(5)
            ->each(function (Ticket $ticket) use ($agent1) {
                $ticket->assignToAgent($agent1)->dueOn('yesterday');
            });

        /*
         * On time for agent 1
         */
        $this->make->tickets(2)
            ->each(function (Ticket $ticket) use ($agent1) {
                $ticket->assignToAgent($agent1)->dueOn('tomorrow');
            });

        /*
         * Overdue for agent 2
         */
        $this->make->tickets(2)
            ->each(function (Ticket $ticket) use ($agent2) {
                $ticket->assignToAgent($agent2)->dueOn('yesterday');
            });

        /*
         * On time for agent 2
         */
        $this->make->tickets(3)
            ->each(function (Ticket $ticket) use ($agent2) {
                $ticket->assignToAgent($agent2)->dueOn('tomorrow');
            });

        $tickets = Tickets::forAgent($agent1)->overdue();

        $this->assertEquals(5, $tickets->count());
    }

    /** @test */
    public function the_overdue_method_returns_all_overdue_tickets_in_supervisor_context()
    {
        $super = $this->make->super;
        $agent1 = $this->make->agent;
        $agent2 = $this->make->agent;

        $this->make->tickets(5)
            ->each(function (Ticket $ticket) use ($agent1) {
                $ticket->assignToAgent($agent1)->dueOn('yesterday');
            });

        $this->make->tickets(2)
            ->each(function (Ticket $ticket) use ($agent1) {
                $ticket->assignToAgent($agent1)->dueOn('tomorrow');
            });

        $this->make->tickets(6)
            ->each(function (Ticket $ticket) use ($agent2) {
                $ticket->assignToAgent($agent2)->dueOn('yesterday');
            });

        $this->make->tickets(3)
            ->each(function (Ticket $ticket) use ($agent2) {
                $ticket->assignToAgent($agent2)->dueOn('tomorrow');
            });

        $tickets = Tickets::forSuper($super)->overdue();

        $this->assertEquals(11, $tickets->count());
    }

    /** @test */
    public function the_team_method_returns_null_if_agent_is_not_set()
    {
        $user = $this->make->user;

        $this->make->tickets(5, $user);

        $tickets = Tickets::forUser($user)->team();

        $this->assertNull($tickets);
    }

    /** @test */
    public function the_team_method_returns_tickets_assigned_to_the_agents_team()
    {
        $agent = $this->make->agent;
        $team1 = $this->make->team;
        $team2 = $this->make->team;

        $this->make->tickets(5)
            ->each(function (Ticket $ticket) use ($team1) {
                $ticket->assignToTeam($team1);
            });

        $this->make->tickets(9)
            ->each(function (Ticket $ticket) use ($team2) {
                $ticket->assignToTeam($team2);
            });

        $agent->addToTeam($team1);
        $tickets = Tickets::forAgent($agent)->team();

        $this->assertEquals(5, $tickets->count());
    }

    /** @test */
    public function the_team_method_returns_all_team_assigned_in_supervisor_context()
    {
        $super = $this->make->super;
        $agent = $this->make->agent;
        $team = $this->make->team;
        $team2 = factory(Team::class)->create();

        $this->make->tickets(5)
            ->each(function (Ticket $ticket) use ($team) {
                $ticket->assignToTeam($team);
            });

        $this->make->tickets(5)
            ->each(function (Ticket $ticket) use ($team2) {
                $ticket->assignToTeam($team2);
            });

        $agent->addToTeam($team);
        $tickets = Tickets::forSuper($super)->team();

        $this->assertEquals(10, $tickets->count());
    }

    /** @test */
    public function the_team_method_returns_tickets_assigned_to_multiple_agent_teams()
    {
        $agent = $this->make->agent;
        $team1 = $this->make->team;
        $team2 = $this->make->team;
        $team3 = $this->make->team;

        $this->make->tickets(2)
            ->each(function (Ticket $ticket) use ($team1) {
                $ticket->assignToTeam($team1);
            });

        $this->make->tickets(18)
            ->each(function (Ticket $ticket) use ($team2) {
                $ticket->assignToTeam($team2);
            });

        $this->make->tickets(7)
            ->each(function (Ticket $ticket) use ($team3) {
                $ticket->assignToTeam($team3);
            });

        $agent->addToTeams([$team1, $team3]);
        $tickets = Tickets::forAgent($agent)->team();

        $this->assertEquals(9, $tickets->count());
    }

    /** @test */
    public function the_unassigned_method_returns_null_for_users()
    {
        $user = $this->make->user;

        $this->make->tickets(11);

        $tickets = Tickets::forUser($user)->unassigned();

        $this->assertNull($tickets);
    }

    /** @test */
    public function the_unassigned_method_returns_null_for_agents()
    {
        $agent = $this->make->agent;

        $this->make->tickets(11);

        $tickets = Tickets::forAgent($agent)->unassigned();

        $this->assertNull($tickets);
    }

    /** @test */
    public function the_unassigned_method_return_unassigned_tickets_for_supers()
    {
        $super = $this->make->super;

        $this->make->tickets(11);

        $tickets = Tickets::forSuper($super)->unassigned();

        $this->assertEquals(11, $tickets->count());
    }

    /** @test */
    public function collaborating_returns_tickets_the_agent_is_a_collaborator_on()
    {
        $assignee = $this->make->agent;
        $collaborator1 = $this->make->agent;
        $collaborator2 = $this->make->super;

        $this->make->ticket->assignToAgent($assignee);
        $ticket2 = $this->make->ticket->assignToAgent($assignee);
        $ticket3 = $this->make->ticket->assignToAgent($assignee);

        $ticket2->addCollaborator($collaborator1, $assignee);
        $ticket3->addCollaborator($collaborator2, $assignee);

        $tickets = Tickets::forAgent($collaborator1)->collaborating();

        $this->assertEquals(1, $tickets->count());
    }
}
