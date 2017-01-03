<?php

namespace Aviator\Helpdesk\Models;

use Aviator\Helpdesk\Traits\AutoUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use SoftDeletes, AutoUuids;

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Fields exempt from mass assignment
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    ///////////////////
    // RELATIONSHIPS //
    ///////////////////

    public function user() {
        return $this->belongsTo(
            config('helpdesk.userModel')
        );
    }
}
