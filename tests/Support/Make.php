<?php

namespace Aviator\Helpdesk\Tests\Support;

use Aviator\Helpdesk\Models\Action;
use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Assignment;
use Aviator\Helpdesk\Models\Closing;
use Aviator\Helpdesk\Models\Collaborator;
use Aviator\Helpdesk\Models\DueDate;
use Aviator\Helpdesk\Models\GenericContent;
use Aviator\Helpdesk\Models\Note;
use Aviator\Helpdesk\Models\Opening;
use Aviator\Helpdesk\Models\Reply;
use Aviator\Helpdesk\Models\Team;
use Aviator\Helpdesk\Models\TeamAssignment;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Tests\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Class Create.
 * @property \Aviator\Helpdesk\Models\Agent super
 * @property \Aviator\Helpdesk\Models\Agent agent
 * @property \Aviator\Helpdesk\Models\Team team
 * @property \Aviator\Helpdesk\Models\Ticket ticket
 * @property \Aviator\Helpdesk\Models\Ticket ticketWithDeletedContent
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
 * @property \Aviator\Helpdesk\Models\Agent teamLead
 * @property \Aviator\Helpdesk\Tests\User $internalUser
 * @property \Aviator\Helpdesk\Tests\User $user
 */
class Make
{
    protected string $ticketUriSlug = 'helpdesk/tickets/';

    public function action (): Action
    {
        return factory(Action::class)->create();
    }

    public function assignment (): Assignment
    {
        return factory(Assignment::class)->create();
    }

    public function agent (User $user = null): Agent
    {
        return $user
            ? factory(Agent::class)->create(['user_id' => $user->id])
            : factory(Agent::class)->create();
    }

    public function agentNamed (string $name): Agent
    {
        return factory(Agent::class)->create([
            'user_id' => factory(User::class)->create(['name' => $name])->id,
        ]);
    }

    public function agents (int $count): Collection
    {
        if ($count <= 1) {
            throw new \Exception('Count must be greater than 1.');
        }

        return factory(Agent::class, $count)->create();
    }

    public function closing (): Closing
    {
        return factory(Closing::class)->create();
    }

    public function collaborator (): Collaborator
    {
        return factory(Collaborator::class)->create();
    }

    public function content (): GenericContent
    {
        return factory(GenericContent::class)->create();
    }

    public function dueDate (): DueDate
    {
        return factory(DueDate::class)->create();
    }

    public function note (): Note
    {
        return factory(Note::class)->create();
    }

    public function opening (): Opening
    {
        return factory(Opening::class)->create();
    }

    public function teamAssignment (Team $team = null): TeamAssignment
    {
        $team = $team
            ?: $this->team();

        return factory(TeamAssignment::class)->create([
            'team_id' => $team->id,
        ]);
    }

    public function teamLead (Team $team = null): Agent
    {
        return $this->agent()
            ->makeTeamLeadOf(
                $team
                    ?: $this->team
            );
    }

    public function reply (): Reply
    {
        return factory(Reply::class)->create();
    }

    public function super (): Agent
    {
        return factory(Agent::class)->states('isSuper')->create();
    }

    public function user (): User
    {
        return factory(User::class)->create();
    }

    public function internalUser (): User
    {
        return factory(User::class)->states('isInternal')->create();
    }

    public function ticket (User $user = null, string $when = 'now'): Ticket
    {
        $user = $user
            ?: factory(User::class)->create();

        return factory(Ticket::class)->create([
            'user_id' => $user->id,
            'created_at' => Carbon::parse($when),
        ]);
    }

    public function ticketWithDeletedContent (): Ticket
    {
        return factory(Ticket::class)->create([
            'content_type' => 'Foo\\Bar\\DeletedContent',
        ]);
    }

    public function tickets (int $quantity, User $user = null): Collection
    {
        if ($quantity <= 1) {
            throw new \Exception('Quantity must be greater than 1.');
        }

        $user = $user
            ?: factory(User::class)->create();

        return factory(Ticket::class, $quantity)->create([
            'user_id' => $user->id,
        ]);
    }

    public function ticketUri (Ticket $ticket = null): string
    {
        return $ticket
            ? sprintf('%s%s', $this->ticketUriSlug, $ticket->id)
            : $this->ticketUriSlug;
    }

    public function ticketUuidUri (Ticket $ticket): string
    {
        return sprintf('%spublic/%s', $this->ticketUriSlug, $ticket->uuid);
    }

    public function team (): Team
    {
        return factory(Team::class)->create();
    }

    public function option (Agent $agent, string $idSlug): string
    {
        return sprintf(
            '<option value="%s" id="%s%s">%s</option>',
            $agent->id,
            $idSlug,
            $agent->id,
            $agent->user->name
        );
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
