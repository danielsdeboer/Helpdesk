<?php

namespace Aviator\Helpdesk\Helpers\Ticket;

use Aviator\Helpdesk\Interfaces\TicketContent;
use Aviator\Helpdesk\Models\Ticket;

class Contents extends TicketHelper
{
    /**
     * Associate the content model with a ticket.
     * @param TicketContent $content
     * @return Ticket
     */
    public function add (TicketContent $content) : Ticket
    {
        /** @noinspection PhpParamsInspection */
        $this->ticket->content()
            ->associate($content)
            ->save();

        return $this->ticket;
    }

    /**
     * Create and associate the ticket content.
     * @param \Aviator\Helpdesk\Interfaces\TicketContent $content
     * @param array $attributes
     * @return Ticket
     */
    public function create (TicketContent $content, array $attributes) : Ticket
    {
        $content->fill($attributes)->save();

        return $this->add($content);
    }
}
