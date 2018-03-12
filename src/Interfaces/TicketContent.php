<?php

namespace Aviator\Helpdesk\Interfaces;

interface TicketContent
{
    /**
     * @return string
     */
    public function partial () : string;

    /**
     * @return string
     */
    public function title () : string;

    /**
     * Inherited from Model.
     * @param array $attributes
     * @return mixed
     */
    public function fill (array $attributes);

    /**
     * Inherited from Model.
     * @param array $options
     * @return mixed
     */
    public function save(array $options = []);
}
