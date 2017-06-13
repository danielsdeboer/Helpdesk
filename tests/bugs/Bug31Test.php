<?php

namespace Aviator\Helpdesk\Tests;

class Bug31Test extends AdminBase
{
    const PUB = 'helpdesk/tickets/public/';

    /**
     * @group bugs
     * @group bugs.31
     * @test
     */
    public function openTicketsShouldBeSortedByDueDateSoonestFirst()
    {
    }
}
