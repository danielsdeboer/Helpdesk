<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Pool;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Repositories\Tickets;
use Aviator\Helpdesk\Tests\TestCase;
use Aviator\Helpdesk\Tests\User;

class TicketsTest extends TestCase
{
    /**
     * @group repo
     * @group repo.tickets
     * @test
     */
    public function the_foruser_static_returns_an_instance_with_the_user_set()
    {
        $user = factory(User::class)->create();

        $tickets = Tickets::forUser($user);

        $this->assertInstanceOf(Tickets::class, $tickets);
        $this->assertSame($user, $tickets->getUser());
    }

    /**
     * @group repo
     * @group repo.tickets
     * @test
     */
    public function the_foragent_static_returns_an_instance_with_the_agent_and_user_set()
    {
        $agent = factory(Agent::class)->create();

        $tickets = Tickets::forAgent($agent);

        $this->assertInstanceOf(Tickets::class, $tickets);
        $this->assertSame($agent->user, $tickets->getUser());
        $this->assertSame($agent, $tickets->getAgent());
    }

    /**
     * @group repo
     * @group repo.tickets
     * @test
     */
    public function the_forsuper_static_return_an_instance_with_agent_user_and_super_set()
    {
        $agent = factory(Agent::class)->states('isSuper')->create();

        $tickets = Tickets::forSuper($agent);

        $this->assertInstanceOf(Tickets::class, $tickets);
        $this->assertSame($agent->user, $tickets->getUser());
        $this->assertSame($agent, $tickets->getAgent());
        $this->assertTrue($tickets->getSuper());
    }

    /**
     * @group repo
     * @group repo.tickets
     * @test
     */
    public function the_forsuper_static_only_sets_super_to_true_if_the_agent_is_the_supervisor()
    {
        $super = factory(Agent::class)->states('isSuper')->create();
        $notSuper = factory(Agent::class)->create();

        $superTickets = Tickets::forSuper($super);
        $notSuperTickets = Tickets::forSuper($notSuper);

        $this->assertTrue($superTickets->getSuper());
        $this->assertNull($notSuperTickets->getSuper());
    }

    /**
     * @group repo
     * @group repo.tickets
     * @test
     */
    public function the_all_method_returns_only_open_tickets_owned_by_the_user()
    {
        $user = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $userTickets = factory(Ticket::class, 10)->create([
            'user_id' => $user->id
        ]);
        $user2Tickets = factory(Ticket::class, 10)->create([
            'user_id' => $user2->id
        ]);

        $userTickets = Ticket::with('user')->whereIn('id', $userTickets->pluck('id'))->get();
        $tickets = Tickets::forUser($user)->all();

        $this->assertEquals(10, $tickets->count());
        $this->assertEquals($userTickets->toArray(), $tickets->toArray());
    }

    /**
     * @group repo
     * @group repo.tickets
     * @test
     */
    public function the_all_method_returns_only_open_tickets_assigned_to_the_agent()
    {
        $agent = factory(Agent::class)->create();
        $agent2 = factory(Agent::class)->create();

        $agentTickets = factory(Ticket::class, 5)->create()->each(function($item) use ($agent) {
            $item->assignToAgent($agent);
        });

        $agent2Tickets = factory(Ticket::class, 6)->create()->each(function($item) use ($agent2) {
            $item->assignToAgent($agent2);
        });

        $tickets = Tickets::forAgent($agent)->all();

        $this->assertEquals(5, $tickets->count());
    }

    /**
     * @group repo
     * @group repo.tickets
     * @test
     */
    public function the_all_method_returns_all_open_tickets_in_supervisor_context()
    {
        $super = factory(Agent::class)->states('isSuper')->create();
        $agent = factory(Agent::class)->create();
        $agent2 = factory(Agent::class)->create();

        $agentTickets = factory(Ticket::class, 5)->create()->each(function($item) use ($agent) {
            $item->assignToAgent($agent);
        });

        $agent2Tickets = factory(Ticket::class, 6)->create()->each(function($item) use ($agent2) {
            $item->assignToAgent($agent2);
        });

        $tickets = Tickets::forSuper($super)->all();

        $this->assertEquals(11, $tickets->count());
    }

    /**
     * @group repo
     * @group repo.tickets
     * @test
     */
    public function the_overdue_method_returns_only_overdue_tickets_owned_by_the_user()
    {
        $user = factory(User::class)->create();
        $user2 = factory(User::class)->create();

        $overdueTickets = factory(Ticket::class, 5)->create([
            'user_id' => $user->id
        ])->each(function($item) {
            $item->dueOn('yesterday');
        });

        $notOverdueTickets = factory(Ticket::class, 10)->create([
            'user_id' => $user2->id
        ])->each(function($item) {
            $item->dueOn('tomorrow');
        });

        $otherOverdueTickets = factory(Ticket::class, 6)->create()->each(function($item) {
            $item->dueOn('yesterday');
        });

        $overdueTickets = Ticket::with('user', 'dueDate')->where('user_id', $user->id)->overdue()->get();
        $tickets = Tickets::forUser($user)->overdue();

        $this->assertEquals(5, $tickets->count());
        $this->assertEquals($overdueTickets->toArray(), $tickets->toArray());
    }

    /**
     * @group repo
     * @group repo.tickets
     * @test
     */
    public function the_overdue_method_returns_only_overdue_tickets_assigned_to_the_agent()
    {
        $agent = factory(Agent::class)->create();
        $agent2 = factory(Agent::class)->create();

        $agentOverdueTickets = factory(Ticket::class, 5)->create()->each(function($item) use ($agent) {
            $item->assignToAgent($agent)->dueOn('yesterday');
        });

        $agentOnTimeTickets = factory(Ticket::class, 2)->create()->each(function($item) use ($agent) {
            $item->assignToAgent($agent)->dueOn('tomorrow');
        });

        $agent2OverdueTickets = factory(Ticket::class, 6)->create()->each(function($item) use ($agent2) {
            $item->assignToAgent($agent2)->dueOn('yesterday');
        });

        $agent2OnTimeTickets = factory(Ticket::class, 3)->create()->each(function($item) use ($agent2) {
            $item->assignToAgent($agent2)->dueOn('tomorrow');
        });

        $tickets = Tickets::forAgent($agent)->overdue();

        $this->assertEquals(5, $tickets->count());
    }

    /**
     * @group repo
     * @group repo.tickets
     * @test
     */
    public function the_overdue_method_returns_all_overdue_tickets_in_supervisor_context()
    {
        $super = factory(Agent::class)->states('isSuper')->create();
        $agent = factory(Agent::class)->create();
        $agent2 = factory(Agent::class)->create();

        $agentOverdueTickets = factory(Ticket::class, 5)->create()->each(function($item) use ($agent) {
            $item->assignToAgent($agent)->dueOn('yesterday');
        });

        $agentOnTimeTickets = factory(Ticket::class, 2)->create()->each(function($item) use ($agent) {
            $item->assignToAgent($agent)->dueOn('tomorrow');
        });

        $agent2OverdueTickets = factory(Ticket::class, 6)->create()->each(function($item) use ($agent2) {
            $item->assignToAgent($agent2)->dueOn('yesterday');
        });

        $agent2OnTimeTickets = factory(Ticket::class, 3)->create()->each(function($item) use ($agent2) {
            $item->assignToAgent($agent2)->dueOn('tomorrow');
        });

        $tickets = Tickets::forSuper($super)->overdue();

        $this->assertEquals(11, $tickets->count());
    }

    /**
     * @group repo
     * @group repo.tickets
     * @test
     */
    public function the_team_method_returns_null_if_agent_is_not_set()
    {
        $user = factory(User::class)->create();

        $userTickets = factory(Ticket::class, 5)->create([
            'user_id' => $user->id
        ]);

        $tickets = Tickets::forUser($user)->team();

        $this->assertNull($tickets);
    }

    /**
     * @group repo
     * @group repo.tickets
     * @test
     */
    public function the_team_method_returns_tickets_assigned_to_the_agents_team()
    {
        $agent = factory(Agent::class)->create();
        $team = factory(Pool::class)->create();
        $team2 = factory(Pool::class)->create();

        $teamTickets = factory(Ticket::class, 5)->create()->each(function($item) use ($team) {
            $item->assignToPool($team);
        });

        $team2Tickets = factory(Ticket::class, 5)->create()->each(function($item) use ($team2) {
            $item->assignToPool($team2);
        });

        $agent->addToTeam($team);
        $tickets = Tickets::forAgent($agent)->team();

        $this->assertEquals(5, $tickets->count());
    }

    /**
     * @group repo
     * @group repo.tickets
     * @test
     */
    public function the_team_method_returns_all_team_assigned_in_supervisor_context()
    {
        $super = factory(Agent::class)->states('isSuper')->create();
        $agent = factory(Agent::class)->create();
        $team = factory(Pool::class)->create();
        $team2 = factory(Pool::class)->create();

        $teamTickets = factory(Ticket::class, 5)->create()->each(function($item) use ($team) {
            $item->assignToPool($team);
        });

        $team2Tickets = factory(Ticket::class, 5)->create()->each(function($item) use ($team2) {
            $item->assignToPool($team2);
        });

        $agent->addToTeam($team);
        $tickets = Tickets::forSuper($super)->team();

        $this->assertEquals(10, $tickets->count());
    }

    /**
     * @group repo
     * @group repo.tickets
     * @test
     */
    public function the_team_method_returns_tickets_assigned_to_multiple_agent_teams()
    {
        $agent = factory(Agent::class)->create();
        $team = factory(Pool::class)->create();
        $team2 = factory(Pool::class)->create();
        $team3 = factory(Pool::class)->create();

        $teamTickets = factory(Ticket::class, 5)->create()->each(function($item) use ($team) {
            $item->assignToPool($team);
        });

        $team2Tickets = factory(Ticket::class, 5)->create()->each(function($item) use ($team2) {
            $item->assignToPool($team2);
        });

        $team3Tickets = factory(Ticket::class, 3)->create()->each(function($item) use ($team3) {
            $item->assignToPool($team3);
        });

        $agent->addToTeams([$team, $team3]);
        $tickets = Tickets::forAgent($agent)->team();

        $this->assertEquals(8, $tickets->count());
    }

    /**
     * @group repo
     * @group repo.tickets
     * @test
     */
    public function the_unassigned_method_returns_null_for_users()
    {
        $user = factory(User::class)->create();

        $unassignedTickets = factory(Ticket::class, 11)->create();

        $tickets = Tickets::forUser($user)->unassigned();

        $this->assertNull($tickets);
    }

    /**
     * @group repo
     * @group repo.tickets
     * @test
     */
    public function the_unassigned_method_returns_null_for_agents()
    {
        $agent = factory(Agent::class)->create();

        $unassignedTickets = factory(Ticket::class, 11)->create();

        $tickets = Tickets::forAgent($agent)->unassigned();

        $this->assertNull($tickets);
    }

    /**
     * @group repo
     * @group repo.tickets
     * @test
     */
    public function the_unassigned_method_return_unassigned_tickets_for_supers()
    {
        $super = factory(Agent::class)->states('isSuper')->create();

        $unassignedTickets = factory(Ticket::class, 4)->create();

        $tickets = Tickets::forSuper($super)->unassigned();

        $this->assertEquals(4, $tickets->count());
    }
}