<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Pool;
use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Ticket;

class Bug17Test extends AdminBase
{
    const VERB = 'GET';
    const URIBASE = 'helpdesk/tickets/';
    const URI = 'helpdesk/tickets/1';
    const PUB = 'helpdesk/tickets/public/';

    /**
     * @group bugs
     * @group bugs.17
     * @test
     */
    public function ticketShouldHaveOverdueStatusTag()
    {
        $ticket = factory(Ticket::class)->create();
        $ticket->dueOn('yesterday');

        $this->visit(self::PUB . $ticket->uuid)
            ->assertResponseOk()
            ->see($ticket->content->title())
            ->see('<span class="tag is-danger is-medium">Overdue</span>');
    }

    /**
     * @group bugs
     * @group bugs.17
     * @test
     */
    public function ticketShouldHaveOnTimeStatusTag()
    {
        $ticket = factory(Ticket::class)->create();
        $ticket->dueOn('tomorrow');

        $this->visit(self::PUB . $ticket->uuid)
            ->assertResponseOk()
            ->see($ticket->content->title())
            ->see('<span class="tag is-success is-medium">On Time</span>');
    }

    /**
     * @group bugs
     * @group bugs.17
     * @test
     */
    public function ticketShouldHaveUnassignedStatusTag()
    {
        $ticket = factory(Ticket::class)->create();

        $this->visit(self::PUB . $ticket->uuid)
            ->assertResponseOk()
            ->see($ticket->content->title())
            ->see('<span class="tag is-danger is-medium">Not Assigned</span>')
            ->dontSee('<span class="tag is-success is-medium">Assigned</span>');
    }

    /**
     * @group bugs
     * @group bugs.17
     * @test
     */
    public function ticketShouldHaveAssignedStatusTag()
    {
        $ticket = factory(Ticket::class)->create();
        $agent = factory(Agent::class)->create();

        $ticket->assignToAgent($agent);

        $this->visit(self::PUB . $ticket->uuid)
            ->assertResponseOk()
            ->see($ticket->content->title())
            ->see('<span class="tag is-success is-medium">Assigned</span>')
            ->dontSee('<span class="tag is-danger is-medium">Not Assigned</span>')
            ->dontSee('<span class="tag is-warning is-medium">Assigned To Team</span>');
    }

    /**
     * @group bugs
     * @group bugs.17
     * @test
     */
    public function ticketShouldHaveAssignedToTeamStatusTag()
    {
        $ticket = factory(Ticket::class)->create();
        $team = factory(Pool::class)->create();

        $ticket->assignToPool($team);

        $this->visit(self::PUB . $ticket->uuid)
            ->assertResponseOk()
            ->see($ticket->content->title())
            ->dontSee('<span class="tag is-success is-medium">Assigned</span>')
            ->dontSee('<span class="tag is-danger is-medium">Not Assigned</span>')
            ->see('<span class="tag is-warning is-medium">Assigned To Team</span>');
    }
}
