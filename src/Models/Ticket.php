<?php

namespace Aviator\Helpdesk\Models;

use Carbon\Carbon;
use Aviator\Helpdesk\Tests\User;
use Aviator\Helpdesk\Traits\AutoUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Aviator\Helpdesk\Interfaces\TicketContent;
use Aviator\Helpdesk\Exceptions\CreatorRequiredException;
use Aviator\Helpdesk\Exceptions\CreatorMustBeAUserException;
use Aviator\Helpdesk\Exceptions\SupervisorNotFoundException;

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
     * Set the table name from the Helpdesk config.
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
     * be overridden.
     * @param  Agent $agent
     * @param  Agent $creator
     * @return $this
     */
    public function assignToAgent(Agent $agent, Agent $creator = null, $isVisible = false)
    {
        Assignment::create([
            'ticket_id' => $this->id,
            'assigned_to' => $agent->id,
            'agent_id' => $creator ? $creator->id : null,
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
     * be overridden.
     * @param int $pool
     * @param  Agent $creator
     * @param bool $isVisible
     * @return $this
     * @internal param User $user
     */
    public function assignToPool($pool, Agent $creator = null, $isVisible = false)
    {
        PoolAssignment::create([
            'ticket_id' => $this->id,
            'pool_id' => $pool->id,
            'agent_id' => $creator ? $creator->id : null,
            'is_visible' => $isVisible,
        ]);

        return $this;
    }

    /**
     * Alias for assignToTeam.
     * @param  mixed[] $args
     * @return $this
     */
    public function assignToTeam(...$args)
    {
        return $this->assignToPool(...$args);
    }

    /**
     * Add a due date. Optionally set the creator.
     *
     * Visibility is assumed true for due dates since
     * end users will probably want to know this.
     * @param  string $date
     * @param  Agent $creator
     * @param  bool $isVisible
     * @return $this
     */
    public function dueOn($date, Agent $creator = null, $isVisible = true)
    {
        DueDate::create([
            'ticket_id' => $this->id,
            'due_on' => Carbon::parse($date),
            'agent_id' => $creator ? $creator->id : null,
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
     * @param  User | Agent $creator
     * @return $this
     * @throws CreatorRequiredException
     */
    public function close($note, $creator)
    {
        if (! $creator) {
            throw new CreatorRequiredException;
        }

        $userClass = config('helpdesk.userModel');

        Closing::create([
            'ticket_id' => $this->id,
            'note' => $note,
            'agent_id' => $creator instanceof Agent ? $creator->id : null,
            'user_id' => $creator instanceof $userClass ? $creator->id : null,
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
     * @param  User | Agent $creator
     * @return $this
     * @throws CreatorRequiredException
     */
    public function open($note, $creator)
    {
        if (! $creator) {
            throw new CreatorRequiredException;
        }

        $userClass = config('helpdesk.userModel');

        Opening::create([
            'ticket_id' => $this->id,
            'note' => $note,
            'agent_id' => $creator instanceof Agent ? $creator->id : null,
            'user_id' => $creator instanceof $userClass ? $creator->id : null,
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
     * @param  User | Agent $creator
     * @param bool $isVisible
     * @return $this
     * @throws CreatorRequiredException
     */
    public function note($body, $creator, $isVisible = true)
    {
        if (! $creator) {
            throw new CreatorRequiredException;
        }

        $userClass = config('helpdesk.userModel');

        Note::create([
            'ticket_id' => $this->id,
            'body' => $body,
            'agent_id' => $creator instanceof Agent ? $creator->id : null,
            'user_id' => $creator instanceof $userClass ? $creator->id : null,
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
     * @param  Agent $agent
     * @return $this
     */
    public function internalReply($body, Agent $agent)
    {
        Reply::create([
            'ticket_id' => $this->id,
            'body' => $body,
            'agent_id' => $agent->id,
            'is_visible' => true,
        ]);

        return $this;
    }

    /**
     * Add an external reply to the ticket.
     *
     * Visibility is always true for replies
     * @param  string $body
     * @param $user
     * @return $this
     * @throws CreatorMustBeAUserException
     */
    public function externalReply($body, $user)
    {
        $userClass = config('helpdesk.userModel');

        /*
         * Since the user class can vary, throw an exception if something
         * else is provided.
         */
        if (! $user instanceof $userClass) {
            throw new CreatorMustBeAUserException;
        }

        Reply::create([
            'ticket_id' => $this->id,
            'body' => $body,
            'user_id' => $user->id,
            'is_visible' => true,
        ]);

        return $this;
    }

    /**
     * Associate the content model with a ticket.
     * @param TicketContent $content
     * @return $this
     */
    public function withContent(TicketContent $content)
    {
        $this->content()->associate($content);

        $this->save();

        return $this;
    }

    /**
     * Create and assocate the ticket content.
     * @param string $class
     * @param array $attribute
     * @return $this
     */
    public function createContent($class, array $attributes)
    {
        $content = $class::create($attributes);

        $this->content()->associate($content);

        $this->save();

        return $this;
    }

    ////////////////////////
    // NON-FLUENT HELPERS //
    ////////////////////////

    /**
     * Find the internal user who should receive notifications for
     * external user replies, etc.
     * @return mixed
     * @throws SupervisorNotFoundException
     */
    public function getInternalUser()
    {
        $userModel = config('helpdesk.userModel');
        $emailColumn = config('helpdesk.userModelEmailColumn');
        $supervisorEmails = config('helpdesk.supervisors');

        // Check if the ticket is assigned to a particular user
        if (isset($this->assignment->assignee)) {
            return $this->assignment->assignee;
        }

        // If not, check if the ticket is assigned to a pool
        if (isset($this->poolAssignment->pool->teamLead)) {
            return $this->poolAssignment->pool->teamLead;
        }

        // Notify the supervisor
        if ($super = $userModel::whereIn($emailColumn, $supervisorEmails)->first()) {
            return $super;
        }

        // If all else fails, throw an exception
        throw new SupervisorNotFoundException();
    }

    /**
     * Is the ticket open.
     * @return bool
     */
    public function isOpen()
    {
        return $this->status == 'open';
    }

    /**
     * Is the ticket closed.
     * @return bool
     */
    public function isClosed()
    {
        return $this->status == 'closed';
    }

    /**
     * Is the ticket overdue.
     * @return bool
     */
    public function isOverdue()
    {
        return $this->dueDate && $this->dueDate->due_on->lte(Carbon::now());
    }

    /**
     * Is the ticket assigned to an agent or team.
     * @return bool
     */
    public function isAssigned()
    {
        return $this->assignment || $this->poolAssignment;
    }

    /**
     * Is the ticket assigned to an agent.
     * @return bool
     */
    public function isAssignedToAgent()
    {
        return (bool) $this->assignment;
    }

    /**
     * Is the ticket assigned to a team.
     * @return bool
     */
    public function isAssignedToTeam()
    {
        return $this->poolAssignment && ! $this->assignment;
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
     * Find a single model with actions.
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
     * Get tickets assigned to pools.
     */
    public function scopePooled($query)
    {
        return $query->has('poolAssignment')->whereDoesntHave('assignment')->whereStatus('open');
    }

    /**
     * Get overdue tickets.
     */
    public function scopeOverdue($query)
    {
        return $query->whereHas('dueDate', function ($query) {
            $query->where('due_on', '<', Carbon::now()->toDateString());
        })->whereStatus('open');
    }

    /**
     * Get on time tickets. Due Today is a subet of on time tickets.
     */
    public function scopeOnTime($query)
    {
        return $query->whereHas('dueDate', function ($query) {
            $query->where('due_on', '>=', Carbon::now()->toDateString());
        })->whereStatus('open');
    }

    /**
     * Get on time tickets. Due Today is a subet of on time tickets.
     */
    public function scopeDueToday($query)
    {
        return $query->whereHas('dueDate', function ($query) {
            $query->where('due_on', Carbon::now()->toDateString());
        })->whereStatus('open');
    }

    /**
     * Get open tickets. This method name is a bit silly but we're already
     * using open() above. This can be refactored once the open() method
     * is refactored to a builder or domain object.
     */
    public function scopeOpened($query)
    {
        return $query->whereStatus('open');
    }

    /**
     * Get tickets with closed status.
     */
    public function scopeClosed($query)
    {
        return $query->whereStatus('closed');
    }

    /**
     * Get the ticket with actions, sorted oldest to newest.
     */
    public function scopeWithActions($query)
    {
        return $query->with(['actions' => function ($query) {
            $query->orderBy('id', 'asc');
        }]);
    }

    /**
     * Return tickets that are accessible to the current user.
     * @param User | Agent $user
     * @return ticket
     */
    public function scopeAccessible($query, $user)
    {
        $userModel = config('helpdesk.userModel');

        if ($user instanceof $userModel) {
            return $this->accessibleToUser($user);
        }

        if ($user instanceof Agent) {
            return $this->accessibleToAgent($user);
        }
    }

    /**
     * Return tickets that are accessible to the current user.
     * @param User $user
     * @return ticket
     */
    public function scopeAccessibleToUser($query, $user)
    {
        return $query->where(config('helpdesk.tables.tickets') . '.user_id', $user->id);
    }

    /**
     * Return tickets that are accessible to the current agent.
     * @param Agent $agent
     * @return ticket
     */
    public function scopeAccessibleToAgent($query, $agent)
    {
        $supervisorEmails = config('helpdesk.supervisors');
        $email = config('helpdesk.userModelEmailColumn');

        if (in_array($agent->user->$email, $supervisorEmails)) {
            return $query;
        }

        $isTeamLeadOf = $agent->teams->filter(function ($item) {
            return $item->pivot->is_team_lead;
        });

        return $query->where(function ($query) use ($agent, $isTeamLeadOf) {
            $query->whereHas('assignment', function ($query) use ($agent) {
                $query->where('assigned_to', $agent->id);
            })
            ->orWhereHas('poolAssignment', function ($query) use ($isTeamLeadOf) {
                $query->whereIn('pool_id', $isTeamLeadOf->pluck('id')->all());
            });
        });
    }

    ///////////////////
    // RELATIONSHIPS //
    ///////////////////

    public function user()
    {
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

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    public function assignment()
    {
        return $this->hasOne(Assignment::class)->latest();
    }

    public function poolAssignments()
    {
        return $this->hasMany(PoolAssignment::class);
    }

    public function poolAssignment()
    {
        return $this->hasOne(PoolAssignment::class)->latest();
    }

    public function dueDates()
    {
        return $this->hasMany(DueDate::class);
    }

    public function dueDate()
    {
        return $this->hasOne(DueDate::class)->latest();
    }

    public function internalReplies()
    {
        return $this->hasMany(Reply::class)->whereNotNull('agent_id');
    }

    public function externalReplies()
    {
        return $this->hasMany(Reply::class)->whereNotNull('user_id');
    }

    public function closing()
    {
        return $this->hasOne(Closing::class)->latest();
    }

    public function closings()
    {
        return $this->hasMany(Closing::class);
    }

    public function opening()
    {
        return $this->hasOne(Opening::class)->orderBy('id', 'desc');
    }

    public function openings()
    {
        return $this->hasMany(Opening::class);
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }
}
