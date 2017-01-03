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

    /**
     * Set the table name from the Helpdesk config
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('helpdesk.tables.tickets'));
    }

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

    public function assignments() {
        return $this->hasMany(Assignment::class);
    }

    public function assignment() {
        return $this->hasOne(Assignment::class)->latest();
    }

    public function dueDates() {
        return $this->hasMany(DueDate::class);
    }

    public function dueDate() {
        return $this->hasOne(DueDate::class)->latest();
    }
}
