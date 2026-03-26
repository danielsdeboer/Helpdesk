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
use Exception;
use Illuminate\Support\Collection;

/**
 * Class Create.
 *
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

    public function action(): Action
    {
        return Action::factory()->create();
    }

    public function assignment(): Assignment
    {
        return Assignment::factory()->create();
    }

    public function agent(User|null $user = null): Agent
    {
        return $user
            ? Agent::factory()->create(['user_id' => $user->id])
            : Agent::factory()->create();
    }

    public function agentNamed(string $name): Agent
    {
        return Agent::factory()->create([
            'user_id' => User::factory()->create(['name' => $name])->id,
        ]);
    }

    public function agents(int $count): Collection
    {
        if ($count <= 1) {
            throw new Exception('Count must be greater than 1.');
        }

        return Agent::factory()->count($count)->create();
    }

    public function closing(): Closing
    {
        return Closing::factory()->create();
    }

    public function collaborator(): Collaborator
    {
        return Collaborator::factory()->create();
    }

    public function content(): GenericContent
    {
        return GenericContent::factory()->create();
    }

    public function dueDate(): DueDate
    {
        return DueDate::factory()->create();
    }

    public function note(): Note
    {
        return Note::factory()->create();
    }

    public function opening(): Opening
    {
        return Opening::factory()->create();
    }

    public function teamAssignment(Team|null $team = null): TeamAssignment
    {
        $team = $team
            ?: $this->team();

        return TeamAssignment::factory()->create([
            'team_id' => $team->id,
        ]);
    }

    public function teamLead(Team|null $team = null): Agent
    {
        return $this->agent()
            ->makeTeamLeadOf(
                $team
                    ?: $this->team
            );
    }

    public function reply(): Reply
    {
        return Reply::factory()->create();
    }

    public function super(): Agent
    {
        return Agent::factory()->isSuper()->create();
    }

    public function user(): User
    {
        return User::factory()->create();
    }

    public function internalUser(): User
    {
        return User::factory()->isInternal()->create();
    }

    public function ticket(User|null $user = null, string $when = 'now'): Ticket
    {
        $user = $user
            ?: User::factory()->create();

        return Ticket::factory()->create([
            'user_id' => $user->id,
            'created_at' => Carbon::parse($when),
        ]);
    }

    public function ticketWithDeletedContent(): Ticket
    {
        return Ticket::factory()->create([
            'content_type' => 'Foo\\Bar\\DeletedContent',
        ]);
    }

    public function tickets(int $quantity, User|null $user = null): Collection
    {
        if ($quantity <= 1) {
            throw new Exception('Quantity must be greater than 1.');
        }

        $user = $user
            ?: User::factory()->create();

        return Ticket::factory()->count($quantity)->create([
            'user_id' => $user->id,
        ]);
    }

    public function ticketUri(Ticket|null $ticket = null): string
    {
        return $ticket
            ? sprintf('%s%s', $this->ticketUriSlug, $ticket->id)
            : $this->ticketUriSlug;
    }

    public function ticketUuidUri(Ticket $ticket): string
    {
        return sprintf('%spublic/%s', $this->ticketUriSlug, $ticket->uuid);
    }

    public function team(): Team
    {
        return Team::factory()->create();
    }

    public function option(Agent $agent, string $idSlug): string
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
     * @return mixed
     *
     * @throws \Exception
     */
    public function __get($name)
    {
        if (method_exists($this, $name)) {
            return $this->$name();
        }

        throw new Exception('Property ' . $name . ' not found.');
    }
}
