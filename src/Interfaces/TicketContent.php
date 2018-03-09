<?php

namespace Aviator\Helpdesk\Interfaces;

interface TicketContent
{
    /**
     * @return string
     */
    public function partial() : string;

    /**
     * @return string
     */
    public function title() : string;
}
