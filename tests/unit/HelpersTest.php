<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Helpers\Helpers;
use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Ticket;

class HelpersTest extends TestCase
{
    /**
     * @group unit
     * @group unit.help
     * @test
     */
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

    /**
     * @group unit
     * @group unit.help
     * @test
     */
    public function action_creator_returns_the_user_name()
    {
        $user = factory(User::class)->create();
        $ticket = factory(Ticket::class)->create([
            'user_id' => $user->id
        ]);

        $ticket->close(null, $user);

        $this->assertSame(
            $user->name,
            Helpers::actionCreator($ticket->actions->last())
        );
    }

    /**
     * @group unit
     * @group unit.help
     * @test
     */
    public function action_creator_returns_deleted_user()
    {
        $user = factory(User::class)->create();
        $ticket = factory(Ticket::class)->create([
            'user_id' => $user->id
        ]);

        $ticket->close(null, $user);
        $user->delete();

        $this->assertSame(
            '(deleted user)',
            Helpers::actionCreator($ticket->actions->last())
        );
    }
}
