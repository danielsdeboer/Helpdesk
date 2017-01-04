<?php

namespace Aviator\Helpdesk\Models;

use Aviator\Helpdesk\Models\ActionBase;

class InternalReply extends ActionBase
{
    /**
     * Set the table name from the Helpdesk config
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('helpdesk.tables.internal_replies'));
    }
}