<?php

namespace Aviator\Helpdesk\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int ticket_id
 * @property int id
 * @property Ticket ticket
 */
abstract class ActionBase extends AbstractModel
{
    use SoftDeletes;

    /** @var array */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /** @var array */
    protected $guarded = [];

    /** @var array */
    protected $casts = [
        'is_visible' => 'boolean',
    ];

    /**
     * @return BelongsTo
     */
    public function ticket () : BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * @return BelongsTo
     */
    public function agent () : BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * @return BelongsTo
     */
    public function user () : BelongsTo
    {
        return $this->belongsTo(
            config('helpdesk.userModel')
        );
    }

    /**
     * @return MorphOne
     */
    public function action () : MorphOne
    {
        return $this->morphOne(Action::class, 'object');
    }
}
