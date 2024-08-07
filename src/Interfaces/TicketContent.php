<?php

namespace Aviator\Helpdesk\Interfaces;

interface TicketContent
{
    /**
     * @return string
     */
    public function partial();

    /**
     * @return string
     */
    public function title();

    /**
     * Inherited from Model.
     *
     * @return mixed
     */
    public function fill(array $attributes);

    /**
     * Inherited from Model.
     *
     * @return mixed
     */
    public function save(array $options = []);
}
