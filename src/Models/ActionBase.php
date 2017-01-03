<?php

namespace Aviator\Helpdesk\Models;

use Aviator\Helpdesk\Models\Ticket;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActionBase extends Model
{
    use SoftDeletes;

    /**
     * Properties to be mutated to dates
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $guarded = [];

    /**
     * Is the model visible
     * @return boolean
     */
    public function isVisible() {
        return $this->is_visible == 1;
    }

    /**
     * Owned by ticket
     */
    public function ticket() {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Owned by creator
     */
    public function creator() {
        return $this->belongsTo(config('helpdesk.userModel'), 'created_by');
    }
}