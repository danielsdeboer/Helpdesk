<?php

namespace Aviator\Helpdesk\Models;

use Aviator\Helpdesk\Interfaces\TicketContent;
use Illuminate\Database\Eloquent\SoftDeletes;

abstract class ContentBase extends AbstractModel implements TicketContent
{
    use SoftDeletes;

    /** @var string */
    protected $partial;

    /**
     * Get the partial associated with this piece of content.
     */
    public function partial(): string
    {
        return $this->partial;
    }

    /**
     * Get the title associated with this piece of content.
     */
    public function title(): string
    {
        return $this->title;
    }
}
