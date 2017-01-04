<?php

namespace Aviator\Helpdesk\Models;

use Aviator\Helpdesk\Interfaces\TicketContent;
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
    public function assignToUser($user, $creator = null, $isVisible = false)
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
     * Assign to a pool. Optionally set the creator if the
     * assignment isn't automatically done.
     *
     * Visibility for assignments is assumed to be false as
     * this isn't relevant for the end user but this can
     * be overriden.
     * @param  User $user
     * @param  User $creator
     * @return $this
     */
    public function assignToPool($pool, $creator = null, $isVisible = false)
    {
        PoolAssignment::create([
            'ticket_id' => $this->id,
            'pool_id' => $pool->id,
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
    public function dueOn($date, $creator = null, $isVisible = true)
    {
        DueDate::create([
            'ticket_id' => $this->id,
            'due_on' => Carbon::parse($date),
            'created_by' => $creator ? $creator->id : null,
            'is_visible' => $isVisible,
        ]);
    }

    /**
     * Close the ticket. Optionally set the creator.
     *
     * Visibility is assumed true for closings as a status
     * indicator for the customer
     * @param  string $note
     * @param  User $creator
     * @return $this
     */
    public function close($note = null, $creator = null, $isVisible = true)
    {
        Closing::create([
            'ticket_id' => $this->id,
            'note' => $note,
            'created_by' => $creator ? $creator->id : null,
            'is_visible' => $isVisible,
        ]);

        $this->status = 'closed';

        $this->save();
    }

    /**
     * Open the ticket. Optionally set the creator.
     *
     * Visibility is assumed true for openings as a status
     * indicator for the customer
     * @param  string $note
     * @param  User $creator
     * @return $this
     */
    public function open($note = null, $creator = null, $isVisible = true)
    {
        Opening::create([
            'ticket_id' => $this->id,
            'note' => $note,
            'created_by' => $creator ? $creator->id : null,
            'is_visible' => $isVisible,
        ]);

        $this->status = 'open';

        $this->save();
    }

    /**
     * Associate the content model with a ticket
     * @param TicketContent $content
     */
    public function withContent(TicketContent $content)
    {
        $this->content()->associate($content);
    }

    /**
     * Create and assocate the ticket content
     * @param string $class
     * @param array $attribute
     */
    public function createContent($class, array $attributes)
    {
        $content = $class::create($attributes);

        $this->content()->associate($content);
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

    public function poolAssignments() {
        return $this->hasMany(PoolAssignment::class);
    }

    public function poolAssignment() {
        return $this->hasOne(PoolAssignment::class)->latest();
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

    public function closing() {
        return $this->hasOne(Closing::class)->latest();
    }

    public function closings() {
        return $this->hasMany(Closing::class);
    }

    public function opening() {
        return $this->hasOne(Opening::class)->orderBy('id', 'desc');
    }

    public function openings() {
        return $this->hasMany(Opening::class);
    }
}
