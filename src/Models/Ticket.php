<?php

namespace Aviator\Helpdesk\Models;

use Carbon\Carbon;
use Aviator\Helpdesk\Tests\User;
use Aviator\Helpdesk\Traits\AutoUuids;
use Illuminate\Database\Eloquent\Builder;
use Aviator\Helpdesk\Helpers\Ticket\Status;
use Aviator\Helpdesk\Helpers\Ticket\Contents;
use Illuminate\Database\Eloquent\SoftDeletes;
use Aviator\Helpdesk\Interfaces\TicketContent;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
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
 * @property string|null permalink
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
     * Alias for UUID.
     * @return string|null
     */
    public function getPermalinkAttribute ()
    {
        return $this->uuid;
    }

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
    public function assignToAgent (Agent $agent, Agent $creator = null, $isVisible = false) : self
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
    public function assignToTeam ($team, Agent $creator = null, $isVisible = false) : self
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
     * @return \Aviator\Helpdesk\Helpers\Ticket\Contents
     */
    public function contents () : Contents
    {
        return new Contents($this);
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
    public function dueOn ($date, Agent $creator = null, $isVisible = true) : self
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
    public function close ($note, $creator) : self
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
    public function open ($note, $creator) : self
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
    public function note ($body, $creator, $isVisible = true) : self
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
    public function internalReply ($body, Agent $agent) : self
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
    public function externalReply ($body, $user) : self
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
     * @return $this
     */
    public function withContent (TicketContent $content)
    {
        $this->content()->associate($content);
        $this->save();

        return $this;
    }

    /**
     * @param \Aviator\Helpdesk\Models\Agent $collab
     * @param \Aviator\Helpdesk\Models\Agent $creator
     * @return Ticket
     */
    public function addCollaborator (Agent $collab, Agent $creator) : self
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
    public function removeCollaborator (Agent $agent) : self
    {
        $this->collaborators()->where('agent_id', $agent->id)->delete();

        return $this->fresh('collaborators');
    }

    /**
     * @return \Aviator\Helpdesk\Helpers\Ticket\Status
     */
    public function status ()
    {
        return new Status($this);
    }

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
        /* @noinspection PhpIncompatibleReturnTypeInspection */
        return $query->where('uuid', $uuid)->first();
    }

    /**
     * @param Builder $query
     * @param string $permalink
     * @return Builder
     */
    public function scopePermalink (Builder $query, string $permalink)
    {
        return $query->where('uuid', $permalink);
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
     * @param \Illuminate\Foundation\Auth\User $user
     * @return Builder
     */
    public function scopeAccessibleToUser ($query, Authenticatable $user) : Builder
    {
        return $query->where(
            'user_id',
            $user->id
        );
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

    /**
     * Scope the query to tickets assigned to the given agent's team.
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Aviator\Helpdesk\Models\Agent $agent
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function scopeTeam (Builder $query, Agent $agent)
    {
        return $query->whereHas(
            'teamAssignment',
            function (Builder $query) use ($agent) {
                return $query->whereIn(
                    'team_id',
                    $agent->teams->pluck('id')
                );
            }
        );
    }

    /**
     * Scope the query to tickets the given user is collaborating on.
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Aviator\Helpdesk\Models\Agent $agent
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function scopeCollaborating (Builder $query, Agent $agent)
    {
        return $query->whereHas(
            'collaborators',
            function (Builder $query) use ($agent) {
                return $query->where('agent_id', $agent->id);
            }
        );
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
    public function content ()
    {
        return $this->morphTo()->withTrashed();
    }

    /**
     * @return MorphMany
     */
    public function actions ()
    {
        return $this->morphMany(Action::class, 'subject');
    }

    /**
     * @return HasMany
     */
    public function assignments ()
    {
        return $this->hasMany(Assignment::class);
    }

    /**
     * @return HasOne
     */
    public function assignment ()
    {
        return $this->hasOne(Assignment::class)
            ->latest();
    }

    /**
     * @return HasMany
     */
    public function teamAssignments ()
    {
        return $this->hasMany(TeamAssignment::class);
    }

    /**
     * @return HasOne
     */
    public function teamAssignment ()
    {
        return $this->hasOne(TeamAssignment::class)
            ->latest();
    }

    /**
     * @return HasMany
     */
    public function dueDates ()
    {
        return $this->hasMany(DueDate::class);
    }

    /**
     * @return HasOne
     */
    public function dueDate ()
    {
        return $this->hasOne(DueDate::class)->latest();
    }

    /**
     * @return HasMany
     */
    public function internalReplies ()
    {
        return $this->hasMany(Reply::class)->whereNotNull('agent_id');
    }

    /**
     * @return HasMany
     */
    public function externalReplies ()
    {
        return $this->hasMany(Reply::class)->whereNotNull('user_id');
    }

    /**
     * @return HasOne
     */
    public function closing ()
    {
        return $this->hasOne(Closing::class)->latest();
    }

    /**
     * @return HasMany
     */
    public function closings ()
    {
        return $this->hasMany(Closing::class);
    }

    /**
     * @return HasOne
     */
    public function opening ()
    {
        return $this->hasOne(Opening::class)->orderBy('id', 'desc');
    }

    /**
     * @return HasMany
     */
    public function openings ()
    {
        return $this->hasMany(Opening::class);
    }

    /**
     * @return HasMany
     */
    public function notes ()
    {
        return $this->hasMany(Note::class);
    }

    /**
     * @return HasMany
     */
    public function collaborators ()
    {
        return $this->hasMany(Collaborator::class);
    }
}
