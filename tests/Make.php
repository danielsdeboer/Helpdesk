<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Note;
use Aviator\Helpdesk\Models\Team;
use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Reply;
use Illuminate\Support\Collection;
use Aviator\Helpdesk\Models\Action;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Models\Closing;
use Aviator\Helpdesk\Models\DueDate;
use Aviator\Helpdesk\Models\Opening;
use Aviator\Helpdesk\Models\Assignment;
use Aviator\Helpdesk\Models\Collaborator;
use Aviator\Helpdesk\Models\GenericContent;
use Aviator\Helpdesk\Models\TeamAssignment;

/**
 * Class Create.
 * @property \Aviator\Helpdesk\Models\Agent super
 * @property \Aviator\Helpdesk\Tests\User user
 * @property \Aviator\Helpdesk\Models\Agent agent
 * @property \Aviator\Helpdesk\Models\Team team
 * @property \Aviator\Helpdesk\Tests\User internalUser
 * @property \Aviator\Helpdesk\Models\Ticket ticket
 * @property string ticketUri
 * @property \Aviator\Helpdesk\Models\Action action
 * @property \Aviator\Helpdesk\Models\Assignment assignment
 * @property \Aviator\Helpdesk\Models\Closing closing
 * @property \Aviator\Helpdesk\Models\Collaborator collaborator
 * @property \Aviator\Helpdesk\Models\DueDate dueDate
 * @property \Aviator\Helpdesk\Models\Note note
 * @property \Aviator\Helpdesk\Models\Opening opening
 * @property \Aviator\Helpdesk\Models\Reply reply
 * @property \Aviator\Helpdesk\Models\TeamAssignment teamAssignment
 * @property \Aviator\Helpdesk\Models\GenericContent content
 */
class Make
{
    /** @var string */
    protected $ticketUriSlug = 'helpdesk/tickets/';

    /**
     * @return \Aviator\Helpdesk\Models\Action
     */
    public function action ()
    {
        return factory(Action::class)->create();
    }

    /**
     * @return \Aviator\Helpdesk\Models\Assignment
     */
    public function assignment ()
    {
        return factory(Assignment::class)->create();
    }

    /**
     * @return \Aviator\Helpdesk\Models\Agent
     */
    public function agent ()
    {
        return factory(Agent::class)->create();
    }

    /**
     * @return \Aviator\Helpdesk\Models\Closing
     */
    public function closing ()
    {
        return factory(Closing::class)->create();
    }

    /**
     * @return Collaborator
     */
    public function collaborator ()
    {
        return factory(Collaborator::class)->create();
    }

    /**
     * @return \Aviator\Helpdesk\Models\GenericContent
     */
    public function content ()
    {
        return factory(GenericContent::class)->create();
    }

    /**
     * @return \Aviator\Helpdesk\Models\DueDate
     */
    public function dueDate ()
    {
        return factory(DueDate::class)->create();
    }

    /**
     * @return \Aviator\Helpdesk\Models\Note
     */
    public function note ()
    {
        return factory(Note::class)->create();
    }

    /**
     * @return \Aviator\Helpdesk\Models\Opening
     */
    public function opening ()
    {
        return factory(Opening::class)->create();
    }

    /**
     * @param \Aviator\Helpdesk\Models\Team|null $team
     * @return \Aviator\Helpdesk\Models\TeamAssignment
     */
    public function teamAssignment (Team $team = null)
    {
        $team = $team ?: $this->team();

        return factory(TeamAssignment::class)->create([
            'team_id' => $team->id,
        ]);
    }

    /**
     * @return \Aviator\Helpdesk\Models\Reply
     */
    public function reply ()
    {
        return factory(Reply::class)->create();
    }

    /**
     * @return \Aviator\Helpdesk\Models\Agent
     */
    public function super ()
    {
        return factory(Agent::class)->states('isSuper')->create();
    }

    /**
     * @return \Aviator\Helpdesk\Tests\User
     */
    public function user ()
    {
        return factory(User::class)->create();
    }

    /**
     * @return \Aviator\Helpdesk\Tests\User
     */
    public function internalUser ()
    {
        return factory(User::class)->states('isInternal')->create();
    }

    /**
     * Create a ticket, optionally owned by a user.
     * @param \Aviator\Helpdesk\Tests\User|null $user
     * @return \Aviator\Helpdesk\Models\Ticket
     */
    public function ticket (User $user = null)
    {
        $user = $user ?: factory(User::class)->create();

        return factory(Ticket::class)->create([
            'user_id' => $user->id,
        ]);
    }

    /**
     * Create more than one ticket with optional provided user. If one isn't given one will be created
     * since tickets must be owned.
     * @param int $quantity
     * @param \Aviator\Helpdesk\Tests\User|null $user
     * @return \Illuminate\Support\Collection
     */
    public function tickets (int $quantity, User $user = null) : Collection
    {
        $user = $user ?: factory(User::class)->create();

        return factory(Ticket::class, $quantity)->create([
            'user_id' => $user->id,
        ]);
    }

    /**
     * Create a ticket uri.
     * @param \Aviator\Helpdesk\Models\Ticket|null $ticket
     * @return string
     */
    public function ticketUri (Ticket $ticket = null)
    {
        return $ticket
            ? $this->ticketUriSlug . $ticket->id
            : $this->ticketUriSlug;
    }

    /**
     * Create a ticket uri.
     * @param \Aviator\Helpdesk\Models\Ticket|null $ticket
     * @return string
     */
    public function ticketUuidUri (Ticket $ticket)
    {
        return $this->ticketUriSlug . 'public/' . $ticket->uuid;
    }

    /**
     * @return \Aviator\Helpdesk\Models\Team
     */
    public function team ()
    {
        return factory(Team::class)->create();
    }

    /**
     * @param $name
     * @return mixed
     * @throws \Exception
     */
    public function __get ($name)
    {
        if (method_exists($this, $name)) {
            return $this->$name();
        }

        throw new \Exception('Property ' . $name . ' not found.');
    }
}
