<?php

namespace Aviator\Helpdesk\Models;

use Aviator\Helpdesk\Interfaces\TicketContent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GenericContent extends Model implements TicketContent
{
    use SoftDeletes;

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'title',
        'body'
    ];

    /**
     * The partial that displays this content
     * @var string
     */
    protected $partial = 'helpdesk::tickets.show.content.generic';

    /**
     * Set the table name from the Helpdesk config
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('helpdesk.tables.generic_contents'));
    }

    /**
     * Getter for $this->partial
     * @return string
     */
    public function partial()
    {
        return $this->partial;
    }
}
