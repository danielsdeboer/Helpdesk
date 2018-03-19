<?php

namespace Aviator\Helpdesk\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Aviator\Helpdesk\Traits\MorphsWithTrashed;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property string name
 * @property mixed subject_id
 * @property string subject_type
 * @property mixed object_id
 * @property string object_type
 * @property \Illuminate\Database\Eloquent\Model object
 * @property \Illuminate\Database\Eloquent\Model subject
 */
class Action extends AbstractModel
{
    use SoftDeletes, MorphsWithTrashed;

    /** @var string */
    protected $configKey = 'helpdesk.tables.actions';

    /**
     * @return MorphTo
     */
    public function subject () : MorphTo
    {
        return $this->morphToWithTrashed('subject');
    }

    /**
     * @return MorphTo
     */
    public function object () : MorphTo
    {
        return $this->morphToWithTrashed('object');
    }
}
