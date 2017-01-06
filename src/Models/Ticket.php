<?php

namespace Aviator\Helpdesk\Models;

use Aviator\Helpdesk\Exceptions\CreatorRequiredException;
use Aviator\Helpdesk\Exceptions\SupervisorNotFoundException;
use Aviator\Helpdesk\Interfaces\TicketContent;
use Aviator\Helpdesk\Models\Note;
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

    protected $guarded = [];

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
    // FLUENT HELPERS //
    ////////////////////

    /**
     * Assign to a user. Optionally set the creator if the
     * assignment isn't automatically done.
     *
     * Visibility for assignments is assumed to be false as
     * this isn't relevant for the end user but this can
     * be overriden.
     * @param  Agent $agent
     * @param  User $creator
     * @return $this
     */
    public function assignToAgent(Agent $agent, $creator = null, $isVisible = false)
    {
        Assignment::create([
            'ticket_id' => $this->id,
            'assigned_to' => $agent->id,
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
     * @param  bool $isVisible
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

        return $this;
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
    public function close($note = null, $creator)
    {
        if (! $creator) {
            throw new CreatorRequiredException;
        }

        Closing::create([
            'ticket_id' => $this->id,
            'note' => $note,
            'created_by' => $creator->id,
            'is_visible' => true,
        ]);

        $this->status = 'closed';

        $this->save();

        return $this;
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
    public function open($note = null, $creator)
    {
        if (! $creator) {
            throw new CreatorRequiredException;
        }

        Opening::create([
            'ticket_id' => $this->id,
            'note' => $note,
            'created_by' => $creator->id,
            'is_visible' => true,
        ]);

        $this->status = 'open';

        $this->save();

        return $this;
    }

    /**
     * Add a note to the ticket.
     *
     * @param  string $body
     * @param  User $creator
     * @return $this
     */
    public function note($body, $creator, $isVisible = true)
    {
        Note::create([
            'ticket_id' => $this->id,
            'body' => $body,
            'created_by' => $creator->id,
            'is_visible' => $isVisible,
        ]);

        return $this;
    }

    /**
     * Add an internal reply to the ticket.
     *
     * Visibility is always true for replies as the end user
     * is notified for them and must be able to see the
     * body of the reply.
     * @param  string $body
     * @param  User $creator
     * @return $this
     */
    public function internalReply($body, $creator)
    {
        InternalReply::create([
            'ticket_id' => $this->id,
            'body' => $body,
            'created_by' => $creator->id,
            'is_visible' => true,
        ]);

        return $this;
    }

    /**
     * Add an external reply to the ticket.
     *
     * Visibility is always true for replies
     * @param  string $body
     * @param  User $creator
     * @return $this
     */
    public function externalReply($body, $creator)
    {
        ExternalReply::create([
            'ticket_id' => $this->id,
            'body' => $body,
            'created_by' => $creator->id,
            'is_visible' => true,
        ]);

        return $this;
    }

    /**
     * Associate the content model with a ticket
     * @param TicketContent $content
     */
    public function withContent(TicketContent $content)
    {
        $this->content()->associate($content);

        return $this;
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

        return $this;
    }

    ////////////////////////
    // NON-FLUENT HELPERS //
    ////////////////////////

    /**
     * Find the internal user who should receive notifications for
     * external user replies, etc.
     * @return User
     */
    public function getInternalUser()
    {
        $userModel = config('helpdesk.userModel');
        $emailColumn = config('helpdesk.userModelEmailColumn');
        $supervisorEmail = config('helpdesk.supervisor.email');

        // Check if the ticket is assigned to a particular user
        if (isset($this->assignment->assignee)) {
            return $this->assignment->assignee;
        }

        // If not, check if the ticket is assigned to a pool
        if (isset($this->poolAssignment->pool->teamLead)) {
            return $this->poolAssignment->pool->teamLead;
        }

        // Notify the supervisor
        if ($super = $userModel::where($emailColumn, $supervisorEmail)->first()) {
            return $super;
        }

        // If all else fails, throw an exception
        throw new SupervisorNotFoundException();
    }

    /**
     * Is the ticket open
     * @return boolean
     */
    public function isOpen()
    {
        return $this->status == 'open';
    }

    /**
     * Is the ticket closed
     * @return boolean
     */
    public function isClosed()
    {
        return $this->status == 'closed';
    }

    ////////////
    // SCOPES //
    ////////////

    /**
     * Find a model by uuid. It doesn't make sense to call
     * anything other than first() here so the call is
     * made here automatically.
     * @param  string $uuid
     */
    public function scopeUuid($query, $uuid)
    {
        return $query->where('uuid', $uuid)->first();
    }

    /**
     * Find a single model with actions
     * @param  int $id
     */
    public function scopeFindWithActions($query, $id)
    {
        return $query->with('actions')->find($id);
    }

    /**
     * Get unasssigned tickets. A pool assignment isn't
     * considered an assignment for these purposes since
     * a pool assignment is an automatic intermediary
     * to an assignment to an actual user.
     */
    public function scopeUnassigned($query)
    {
        return $query->whereDoesntHave('assignment')->whereStatus('open');
    }

    /**
     * Get asssigned tickets.
     */
    public function scopeAssigned($query)
    {
        return $query->has('assignment')->whereStatus('open');
    }

    /**
     * Get tickets assigned to pools
     */
    public function scopePooled($query)
    {
        return $query->has('poolAssignment')->whereStatus('open');
    }

    /**
     * Get overdue tickets
     */
    public function scopeOverdue($query)
    {
        return $query->whereHas('dueDate', function($query) {
            $query->where('due_on', '<', Carbon::now()->toDateString());
        })->whereStatus('open');
    }

    /**
     * Get on time tickets. Due Today is a subet of on time tickets
     */
    public function scopeOnTime($query)
    {
        return $query->whereHas('dueDate', function($query) {
            $query->where('due_on', '>=', Carbon::now()->toDateString());
        })->whereStatus('open');
    }

    /**
     * Get on time tickets. Due Today is a subet of on time tickets
     */
    public function scopeDueToday($query)
    {
        return $query->whereHas('dueDate', function($query) {
            $query->where('due_on', Carbon::now()->toDateString());
        })->whereStatus('open');
    }

    /**
     * Get open tickets. This method name is a bit silly but we're already
     * using open() above. This can be refactored once the open() method
     * is refactored to a builder or domain object
     */
    public function scopeOpened($query)
    {
        return $query->whereStatus('open');
    }

    /**
     * Get the ticket with actions, sorted oldest to newest
     */
    public function scopeWithActions($query)
    {
        return $query->with(['actions' => function($query) {
            $query->orderBy('id', 'asc');
        }]);
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

    public function internalReplies() {
        return $this->hasMany(InternalReply::class);
    }

    public function externalReplies() {
        return $this->hasMany(ExternalReply::class);
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

    public function notes() {
        return $this->hasMany(Note::class);
    }
}
