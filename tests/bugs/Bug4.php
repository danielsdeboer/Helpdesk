<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Pool;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Tests\AdminBase;

class Bug4 extends AdminBase
{
    const VERB = 'GET';
    const URIBASE = 'helpdesk/tickets/';
    const URI = 'helpdesk/tickets/1';

    /**
     * @group bugs
     * @group bugs.4
     * @test
     */
    public function accessTest()
    {
        $this->noGuests();
    }
}
