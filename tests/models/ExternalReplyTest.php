<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Exceptions\SupervisorNotFoundException;
use Aviator\Helpdesk\Models\ExternalReply;
use Aviator\Helpdesk\Models\Pool;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Notifications\Internal\Replied;
use Aviator\Helpdesk\Tests\User;
use Illuminate\Support\Facades\Notification;

class ExternalReplyTest extends TestCase {

    /**
     * @group reply
     * @test
     */
    public function creating_an_external_reply_creates_an_action_via_its_observer()
    {
        $reply = factory(ExternalReply::class)->create();

        $this->assertEquals('External Reply Added', $reply->action->name);
    }

    /**
     * @group reply
     * @test
     */
    public function an_external_reply_is_visible_by_default()
    {
        $reply = factory(ExternalReply::class)->create();

        $this->assertTrue($reply->is_visible);
    }

    /**
     * @group reply
     * @test
     */
    public function creating_an_external_reply_sends_a_notification_to_the_assignee()
    {
        $ticket = factory(Ticket::class)->create();
        $internalUser = factory(User::class)->create();

        $ticket->assignToUser($internalUser);
        $reply = factory(ExternalReply::class)->create([
            'ticket_id' => $ticket->id,
        ]);


        Notification::assertSentTo(
            $internalUser,
            Replied::class
        );
    }

    /**
     * @group reply
     * @test
     */
    public function if_no_assignee_is_set_the_notification_goes_to_the_assignment_pools_team_lead()
    {
        $ticket = factory(Ticket::class)->create();
        $pool = factory(Pool::class)->create();

        $ticket->assignToPool($pool);
        $reply = factory(ExternalReply::class)->create([
            'ticket_id' => $ticket->id,
        ]);


        Notification::assertSentTo(
            $pool->teamLead,
            Replied::class
        );
    }

    /**
     * @group reply
     * @test
     */
    public function if_no_assignee_or_pool_assignment_is_set_the_notification_goes_to_the_supervisor()
    {
        $ticket = factory(Ticket::class)->create();
        $reply = factory(ExternalReply::class)->create([
            'ticket_id' => $ticket->id,
        ]);

        $supervisor = User::where('email', 'supervisor@test.com')->first();

        Notification::assertSentTo(
            $supervisor,
            Replied::class
        );
    }

    /**
     * @group reply
     * @test
     */
    public function if_no_supervisor_is_found_an_exception_is_thrown()
    {
        $ticket = factory(Ticket::class)->create();

        // The supervisor is automatically created during setUp() so
        // we need to get rid of it.
        User::where('email', 'supervisor@test.com')->delete();

        try {
            $reply = factory(ExternalReply::class)->create([
                'ticket_id' => $ticket->id,
            ]);
        } catch (SupervisorNotFoundException $e) {
            return;
        }

        $this->fail('A supervisor not found exception should have been thrown');
    }
}