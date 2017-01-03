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

    ////////////////////
    // HELPER METHODS //
    ////////////////////

    public function assignTo($user)
    {
        $assignment = Assignment::create([
            'ticket_id' => $this->id,
            'assigned_to' => $user->id,
            'created_by' => auth()->user()->id,
            'is_visible' => false,
        ]);
    }

    ///////////////////
    // RELATIONSHIPS //
    ///////////////////

    public function user() {
        return $this->belongsTo(
            config('helpdesk.userModel')
        );
    }

    public function content()
    {
        return $this->morphTo()->withTrashed();
    }

    public function actions()
    {
        return $this->morphMany(Action::class, 'subject');
    }

    /**
     * Get all assignments
     */
    public function assignments() {
        return $this->hasMany(Assignment::class);
    }

    /**
     * Get the latest assignment
     */
    public function assignment() {
        return $this->hasOne(Assignment::class)->latest();
    }
}
