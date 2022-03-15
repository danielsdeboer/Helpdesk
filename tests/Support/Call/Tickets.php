<?php

namespace Aviator\Helpdesk\Tests\Support\Call;

use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Tests\Support\CallAbstract;
use Illuminate\Testing\TestResponse;

class Tickets extends CallAbstract
{
    /**
     * @param \Aviator\Helpdesk\Models\Ticket|int $ticket
     */
    public function show ($ticket): TestResponse
    {
        return $this->get(sprintf(
            'helpdesk/tickets/%s',
            $ticket instanceof Ticket
                ? $ticket->getKey()
                : $ticket,
        ));
    }
}
