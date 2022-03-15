<?php

namespace Aviator\Helpdesk\Tests\Unit;

use Aviator\Helpdesk\Helpers\Helpers;
use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Tests\TestCase;
use Aviator\Helpdesk\Tests\User;

class HelpersTest extends TestCase
{
    /** @test */
    public function action_creator_returns_the_agent_name()
    {
        $agent = factory(Agent::class)->create();
        $ticket = factory(Ticket::class)->create();
        $ticket->assignToAgent($agent, $agent);

        $this->assertSame(
            $agent->user->name,
            Helpers::actionCreator($ticket->actions->last())
        );
    }

    /** @test */
    public function action_creator_returns_the_user_name()
    {
        $user = factory(User::class)->create();
        $ticket = factory(Ticket::class)->create([
            'user_id' => $user->id,
        ]);

        $ticket->close(null, $user);

        $this->assertSame(
            $user->name,
            Helpers::actionCreator($ticket->actions->last())
        );
    }

    /** @test */
    public function action_creator_returns_deleted_user()
    {
        $user = factory(User::class)->create();
        $ticket = factory(Ticket::class)->create([
            'user_id' => $user->id,
        ]);

        $ticket->close(null, $user);
        $user->delete();

        $this->assertSame(
            '(deleted user)',
            Helpers::actionCreator($ticket->actions->last())
        );
    }
}
