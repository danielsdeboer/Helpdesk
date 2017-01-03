<?php

namespace Aviator\Helpdesk\Models;

use Aviator\Helpdesk\Traits\AutoUuids;
use Carbon\Carbon;
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

    /**
     * Assign to a user. Optionally set the creator if the
     * assignment isn't automatically done.
     *
     * Visibility for assignments is assumed to be false as
     * this isn't relevant for the end user but this can
     * be overriden.
     * @param  User $user
     * @param  User $creator
     * @return $this
     */
    public function assignTo($user, $isVisible = false, $creator = null)
    {
        Assignment::create([
            'ticket_id' => $this->id,
            'assigned_to' => $user->id,
            'created_by' => $creator ? $creator->id : null,
            'is_visible' => $isVisible,
        ]);

        return $this;
    }

    /**
     * Add a due date. Optionally set the creator.
     *
     * Visibility is assumed true for due dates since
     * end users will probably want to know this.
     * @param  string $date
     * @param  User $creator
     * @return $this
     */
    public function dueOn($date, $isVisible = false, $creator = null)
    {
        DueDate::create([
            'ticket_id' => $this->id,
            'due_on' => Carbon::parse($date),
            'created_by' => $creator ? $creator->id : null,
            'is_visible' => $isVisible,
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

    public function emails() {
        return $this->hasMany(Email::class);
    }
}
