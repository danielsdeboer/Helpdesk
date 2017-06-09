<?php

namespace Aviator\Helpdesk\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Aviator\Helpdesk\Interfaces\TicketContent;

class ContentBase extends Model implements TicketContent
{
    use SoftDeletes;

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $partial;

    /**
     * Getter for $this->partial.
     * @return string
     */
    public function partial()
    {
        return $this->partial;
    }

    /**
     * Getter for $this->title.
     * @return string
     */
    public function title()
    {
        return $this->title;
    }
}
