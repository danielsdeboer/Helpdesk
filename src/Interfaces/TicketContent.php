<?php

namespace Aviator\Helpdesk\Interfaces;

interface TicketContent
{
    /**
     * @return string
     */
    public function partial ();

    /**
     * @return string
     */
    public function title ();

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
