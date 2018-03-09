<?php

namespace Aviator\Helpdesk\Models;

use Carbon\Carbon;
use Aviator\Helpdesk\Tests\User;
use Aviator\Helpdesk\Traits\AutoUuids;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Aviator\Helpdesk\Interfaces\TicketContent;
use Aviator\Helpdesk\Exceptions\CreatorRequiredException;
use Aviator\Helpdesk\Exceptions\CreatorMustBeAUserException;
use Aviator\Helpdesk\Exceptions\SupervisorNotFoundException;

/**
 * Class Ticket.
 * @property \Aviator\Helpdesk\Models\TeamAssignment teamAssignment
 * @property mixed user
 * @property mixed id
 * @property mixed collaborators
 * @property string status
 * @property \Aviator\Helpdesk\Models\Assignment assignment
 * @property mixed uuid
 * @property \Aviator\Helpdesk\Models\GenericContent content
 * @property \Illuminate\Support\Collection actions
 * @property \Aviator\Helpdesk\Models\DueDate dueDate
 * @property \Aviator\Helpdesk\Models\Closing closing
 * @property \Aviator\Helpdesk\Models\Opening opening
 * @property \Aviator\Helpdesk\Models\Note notes
 * @property Agent agent
 * @property int user_id
 * @method Builder accessibleToUser($user)
 * @method Builder accessibleToAgent($user)
 * @method static Builder assigned()
 * @method static Builder unassigned()
 * @method static Builder overdue()
 * @method static Builder onTime()
 * @method static Builder dueToday()
 * @method static Builder teamed()
 * @method static Builder accessible($user)
 */
class Ticket extends AbstractModel
{
    use SoftDeletes, AutoUuids;

    /** @var string */
    protected $configKey = 'helpdesk.tables.tickets';
    
    /** @var array */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /** @var array */
    protected $guarded = [];

    /**
     * Find a single model with actions.
     * @param int $id
     * @return \Aviator\Helpdesk\Models\Ticket|null
     */
    public static function findWithActions ($id)
    {
        /** @var \Aviator\Helpdesk\Models\Ticket $ticket */
        $ticket = static::query()->with('actions')->find($id);

        return $ticket;
    }

    /*
     * Fluent Methods
     */

    /**
     * Assign to a user. Optionally set the creator if the
     * assignment isn't automatically done.
     *
     * Visibility for assignments is assumed to be false as
     * this isn't relevant for the end user but this can
     * be overridden.
     * @param  Agent $agent
     * @param  Agent $creator
     * @param bool $isVisible
     * @return Ticket
     */
    public function assignToAgent (Agent $agent, Agent $creator = null, $isVisible = false) : Ticket
    {
        Assignment::query()
            ->create([
                'ticket_id' => $this->id,
                'assigned_to' => $agent->id,
                'agent_id' => $creator ? $creator->id : null,
                'is_visible' => $isVisible,
            ]);

        return $this;
    }

    /**
     * Assign to a team. Optionally set the creator if the assignment isn't automatically done.
     * Visibility for assignments is assumed to be false as this isn't relevant for the end
     * user but this can be overridden.
     * @param \Aviator\Helpdesk\Models\Team $team
     * @param \Aviator\Helpdesk\Models\Agent $creator
     * @param bool $isVisible
     * @return Ticket
     */
    public function assignToTeam ($team, Agent $creator = null, $isVisible = false) : Ticket
    {
        TeamAssignment::query()
            ->create([
                'ticket_id' => $this->id,
                'team_id' => $team->id,
                'agent_id' => $creator ? $creator->id : null,
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
     * @param  Agent $creator
     * @param  bool $isVisible
     * @return Ticket
     */
    public function dueOn ($date, Agent $creator = null, $isVisible = true) : Ticket
    {
        DueDate::query()
            ->create([
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
     * @param  User|Agent $creator
     * @return Ticket
     * @throws CreatorRequiredException
     */
    public function close ($note, $creator) : Ticket
    {
        if (! $creator) {
            throw new CreatorRequiredException('An agent or user must be provided when closing a ticket.');
        }

        $userClass = config('helpdesk.userModel');

        Closing::query()
            ->create([
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
     * @return Ticket
     * @throws CreatorRequiredException
     */
    public function open ($note, $creator) : Ticket
    {
        if (! $creator) {
            throw new CreatorRequiredException('A user or agent is required when opening a ticket.');
        }

        $userClass = config('helpdesk.userModel');

        Opening::query()
            ->create([
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
     * @param  string $body
     * @param  User|Agent $creator
     * @param bool $isVisible
     * @return Ticket
     * @throws CreatorRequiredException
     */
    public function note ($body, $creator, $isVisible = true) : Ticket
    {
        if (! $creator) {
            throw new CreatorRequiredException('A user or agent is required when adding a note.');
        }

        $userClass = config('helpdesk.userModel');

        Note::query()
            ->create([
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
     * @return Ticket
     */
    public function internalReply ($body, Agent $agent) : Ticket
    {
        Reply::query()
            ->create([
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
     * @param User $user
     * @return Ticket
     * @throws CreatorMustBeAUserException
     */
    public function externalReply ($body, $user) : Ticket
    {
        $userClass = config('helpdesk.userModel');

        /*
         * Since the user class can vary, throw an exception if something
         * else is provided.
         */
        if (! $user instanceof $userClass) {
            throw new CreatorMustBeAUserException('External replies may only be created by a user.');
        }

        Reply::query()
            ->create([
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
     * @return Ticket
     */
    public function withContent (TicketContent $content) : Ticket
    {
        /** @noinspection PhpParamsInspection */
        $this->content()->associate($content);

        $this->save();

        return $this;
    }

    /**
     * Create and associate the ticket content.
     * @param string $class
     * @param array $attributes
     * @return Ticket
     */
    public function createContent ($class, array $attributes) : Ticket
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $content = $class::create($attributes);

        $this->content()->associate($content);

        $this->save();

        return $this;
    }

    /**
     * @param \Aviator\Helpdesk\Models\Agent $collab
     * @param \Aviator\Helpdesk\Models\Agent $creator
     * @return Ticket
     */
    public function addCollaborator (Agent $collab, Agent $creator) : Ticket
    {
        $collabs = $this->collaborators()->with('agent')->get();

        if (! $collabs->pluck('agent.id')->contains($collab->id)) {
            Collaborator::query()->create([
                'ticket_id' => $this->id,
                'agent_id' => $collab->id,
                'is_visible' => 1,
                'created_by' => $creator->id,
            ]);
        }

        return $this->fresh('collaborators');
    }

    /**
     * @param \Aviator\Helpdesk\Models\Agent $agent
     * @return Ticket
     */
    public function removeCollaborator (Agent $agent) : Ticket
    {
        $this->collaborators()->where('agent_id', $agent->id)->delete();

        return $this->fresh('collaborators');
    }

    /*
     * Non-fluent public methods
     */

    /**
     * Find the internal user who should receive notifications for
     * external user replies, etc.
     * @return mixed
     * @throws SupervisorNotFoundException
     */
    public function getInternalUser ()
    {
        $userModel = config('helpdesk.userModel');
        $emailColumn = config('helpdesk.userModelEmailColumn');
        $supervisorEmails = config('helpdesk.supervisors');

        // Check if the ticket is assigned to a particular user
        if (isset($this->assignment->assignee)) {
            return $this->assignment->assignee;
        }

        // If not, check if the ticket is assigned to a team
        if (isset($this->teamAssignment->team->teamLead)) {
            return $this->teamAssignment->team->teamLead;
        }

        $super = $super = $userModel::whereIn($emailColumn, $supervisorEmails)->first();

        // Notify the supervisor
        if ($super) {
            return $super;
        }

        // If all else fails, throw an exception
        throw new SupervisorNotFoundException('A supervisor has not been created.');
    }

    /**
     * Is the ticket open.
     * @return bool
     */
    public function isOpen () : bool
    {
        return $this->status === 'open';
    }

    /**
     * Is the ticket closed.
     * @return bool
     */
    public function isClosed () : bool
    {
        return $this->status === 'closed';
    }

    /**
     * Is the ticket overdue.
     * @return bool
     */
    public function isOverdue () : bool
    {
        return $this->dueDate && $this->dueDate->due_on->lte(Carbon::now());
    }

    /**
     * Is the ticket assigned to an agent or team.
     * @return bool
     */
    public function isAssigned () : bool
    {
        return $this->assignment || $this->teamAssignment;
    }

    /**
     * Is the ticket assigned to an agent.
     * @return bool
     */
    public function isAssignedToAnyAgent () : bool
    {
        return (bool) $this->assignment;
    }

    /**
     * Check if the ticket is assigned to a particular agent.
     * @param Agent $agent
     * @return bool
     */
    public function isAssignedTo (Agent $agent) : bool
    {
        return $this->assignment && (int) $this->assignment->assigned_to === (int) $agent->id;
    }

    /**
     * Is the ticket assigned to a team.
     * @return bool
     */
    public function isAssignedToAnyTeam () : bool
    {
        return $this->teamAssignment && ! $this->assignment;
    }

    /**
     * @param \Aviator\Helpdesk\Models\Team $team
     * @return bool
     */
    public function isAssignedToTeam (Team $team) : bool
    {
        return $this->teamAssignment->team->id === $team->id;
    }

    /**
     * Is the given agent a collaborator on this ticket?
     * @param \Aviator\Helpdesk\Models\Agent $agent
     * @return bool
     */
    public function hasCollaborator (Agent $agent) : bool
    {
        return $this->collaborators->pluck('agent.id')->contains($agent->id);
    }

    /**
     * Check if the ticket is owned by a user.
     * @param $user
     * @return bool
     */
    public function isOwnedBy ($user) : bool
    {
        return (int) $user->id === (int) $this->user_id;
    }

    /*
     * Scopes
     */

    /**
     * Find a model by uuid. It doesn't make sense to call
     * anything other than first() here so the call is
     * made here automatically.
     * @param Builder $query
     * @param string $uuid
     * @return Ticket|null
     */
    public function scopeUuid (Builder $query, string $uuid)
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $query->where('uuid', $uuid)->first();
    }

    /**
     * Get unassigned tickets. A team assignment isn't
     * considered an assignment for these purposes since
     * a team assignment is an automatic intermediary
     * to an assignment to an actual user.
     * @param Builder $query
     * @return Builder
     */
    public function scopeUnassigned (Builder $query) : Builder
    {
        return $query->whereDoesntHave('assignment')
            ->where('status', 'open');
    }

    /**
     * Get assigned tickets.
     * @param Builder $query
     * @return Builder
     */
    public function scopeAssigned (Builder $query) : Builder
    {
        return $query->has('assignment')
            ->where('status', 'open');
    }

    /**
     * Get tickets assigned to teams.
     * @param Builder $query
     * @return Builder
     */
    public function scopeTeamed (Builder $query) : Builder
    {
        return $query->has('teamAssignment')
            ->whereDoesntHave('assignment')
            ->where('status', 'open');
    }

    /**
     * Get overdue tickets.
     * @param Builder $query
     * @return Builder
     */
    public function scopeOverdue (Builder $query) : Builder
    {
        return $query
            ->whereHas('dueDate', function (Builder $query) {
                $query->where('due_on', '<', Carbon::now()->toDateString());
            })
            ->where('status', 'open');
    }

    /**
     * Get on time tickets. Due Today is a subset of on time tickets.
     * @param Builder $query
     * @return Builder
     */
    public function scopeOnTime (Builder $query) : Builder
    {
        return $query
            ->whereHas('dueDate', function (Builder $query) {
                $query->where('due_on', '>=', Carbon::now()->toDateString());
            })
            ->where('status', 'open');
    }

    /**
     * Get on time tickets. Due Today is a subset of on time tickets.
     * @param Builder $query
     * @return Builder
     */
    public function scopeDueToday (Builder $query) : Builder
    {
        return $query
            ->whereHas('dueDate', function (Builder $query) {
                $query->where('due_on', Carbon::now()->toDateString());
            })
            ->where('status', 'open');
    }

    /**
     * Get open tickets. This method name is a bit silly but we're already
     * using open() above. This can be refactored once the open() method
     * is refactored to a builder or domain object.
     * @param Builder $query
     * @return Builder
     */
    public function scopeOpened (Builder $query) : Builder
    {
        return $query->where('status', 'open');
    }

    /**
     * Get tickets with closed status.
     * @param Builder $query
     * @return Builder
     */
    public function scopeClosed (Builder $query) : Builder
    {
        return $query->where('status', 'closed');
    }

    /**
     * Get the ticket with actions, sorted oldest to newest.
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithActions (Builder $query) : Builder
    {
        return $query->with([
            'actions' => function (MorphMany $query) {
                $query->orderBy('id', 'asc');
            },
        ]);
    }

    /**
     * Return tickets that are accessible to the current user.
     * @param Builder $query
     * @param User|Agent $user
     * @return Builder|null
     */
    public function scopeAccessible (Builder $query, $user)
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
     * @param Builder $query
     * @param User|Agent $user
     * @return Builder
     */
    public function scopeAccessibleToUser ($query, $user) : Builder
    {
        return $query->where(config('helpdesk.tables.tickets') . '.user_id', $user->id);
    }

    /**
     * Return tickets that are accessible to the current agent.
     * @param Builder $query
     * @param Agent $agent
     * @return Builder
     */
    public function scopeAccessibleToAgent ($query, $agent) : Builder
    {
        if ($agent->isSuper()) {
            return $query;
        }

        /** @var \Illuminate\Support\Collection $isTeamLeadOf */
        $isTeamLeadOf = $agent->teams->filter(function ($item) {
            return $item->pivot->is_team_lead;
        });

        return $query->where(function (Builder $query) use ($agent, $isTeamLeadOf) {
            $query->whereHas('assignment', function (Builder $query) use ($agent) {
                $query->where('assigned_to', $agent->id);
            })
            ->orWhereHas('teamAssignment', function (Builder $query) use ($isTeamLeadOf) {
                $query->whereIn('team_id', $isTeamLeadOf->pluck('id')->all());
            })
            ->orWhereHas('collaborators', function (Builder $query) use ($agent) {
                $query->where('agent_id', $agent->id);
            });
        });
    }

    /*
     * Relationships
     */

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
     * @return MorphTo
     */
    public function content () : MorphTo
    {
        return $this->morphTo()->withTrashed();
    }

    /**
     * @return MorphMany
     */
    public function actions () : MorphMany
    {
        return $this->morphMany(Action::class, 'subject');
    }

    /**
     * @return HasMany
     */
    public function assignments () : HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    /**
     * @return HasOne
     */
    public function assignment () : HasOne
    {
        return $this->hasOne(Assignment::class)
            ->latest();
    }

    /**
     * @return HasMany
     */
    public function teamAssignments() : HasMany
    {
        return $this->hasMany(TeamAssignment::class);
    }

    /**
     * @return HasOne
     */
    public function teamAssignment() : HasOne
    {
        return $this->hasOne(TeamAssignment::class)
            ->latest();
    }

    /**
     * @return HasMany
     */
    public function dueDates () : HasMany
    {
        return $this->hasMany(DueDate::class);
    }

    /**
     * @return HasOne
     */
    public function dueDate () : HasOne
    {
        return $this->hasOne(DueDate::class)->latest();
    }

    /**
     * @return HasMany
     */
    public function internalReplies () : HasMany
    {
        return $this->hasMany(Reply::class)->whereNotNull('agent_id');
    }

    /**
     * @return HasMany
     */
    public function externalReplies () : HasMany
    {
        return $this->hasMany(Reply::class)->whereNotNull('user_id');
    }

    /**
     * @return HasOne
     */
    public function closing () : HasOne
    {
        return $this->hasOne(Closing::class)->latest();
    }

    /**
     * @return HasMany
     */
    public function closings () : HasMany
    {
        return $this->hasMany(Closing::class);
    }

    /**
     * @return HasOne
     */
    public function opening () : HasOne
    {
        return $this->hasOne(Opening::class)->orderBy('id', 'desc');
    }

    /**
     * @return HasMany
     */
    public function openings () : HasMany
    {
        return $this->hasMany(Opening::class);
    }

    /**
     * @return HasMany
     */
    public function notes () : HasMany
    {
        return $this->hasMany(Note::class);
    }

    /**
     * @return HasMany
     */
    public function collaborators () : HasMany
    {
        return $this->hasMany(Collaborator::class);
    }
}
