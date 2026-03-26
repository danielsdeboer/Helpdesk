<?php

namespace Aviator\Helpdesk\Tests\Unit;

use Aviator\Helpdesk\Helpers\Helpers;
use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Tests\TestCase;
use Aviator\Helpdesk\Tests\User;
use PHPUnit\Framework\Attributes\Test;

class HelpersTest extends TestCase
{
    #[Test]
    public function action_creator_returns_the_agent_name()
    {
        $agent = Agent::factory()->create();
        $ticket = Ticket::factory()->create();
        $ticket->assignToAgent($agent, $agent);

        $this->assertSame(
            $agent->user->name,
            Helpers::actionCreator($ticket->actions->last())
        );
    }

    #[Test]
    public function action_creator_returns_the_user_name()
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create([
            'user_id' => $user->id,
        ]);

        $ticket->close(null, $user);

        $this->assertSame(
            $user->name,
            Helpers::actionCreator($ticket->actions->last())
        );
    }

    #[Test]
    public function action_creator_returns_deleted_user()
    {
        /** @var User $user */
        $user = User::factory()->create();

        /** @var Ticket $ticket */
        $ticket = Ticket::factory()->create([
            'user_id' => $user->id,
        ]);

        $ticket->close(null, $user);
        $user->delete();

        $this->assertSame(
            'System Process',
            Helpers::actionCreator($ticket->actions->last())
        );
    }
}
