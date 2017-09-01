<?php

namespace Aviator\Helpdesk\Models;

/**
 * @property mixed ticket_id
 * @property mixed id
 * @property \Aviator\Helpdesk\Models\Agent agent
 * @property \Aviator\Helpdesk\Models\Ticket ticket
 * @property mixed action
 * @property mixed created_by
 */
class Collaborator extends ActionBase
{
    /**
     * Fields to be cast.
     * @var array
     */
    protected $casts = [
        'is_visible' => 'boolean',
    ];

    /**
     * Set the table name from the Helpdesk config.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('helpdesk.tables.collaborators'));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy()
    {
        return $this->belongsTo(config('helpdesk.userModel'), 'created_by');
    }
}