<?php

namespace Aviator\Helpdesk\Database\Seeds;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Aviator\Helpdesk\Models\Team;
use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Ticket;

class HelpdeskSeeder extends Seeder
{
    protected $tickets;
    protected $agents;
    protected $teams;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createTickets(52)
            ->createAgents(10)
            ->createTeams(3)
            ->assignTicketsToUsers()
            ->assignTicketsToTeams()
            ->addDueDatesToAssignedOrTeamedTickets()
            ->addInternalRepliesToAssignedTickets(5)
            ->closeRandomTickets(10);
    }

    /**
     * Create a batch of tickets.
     * @param  int $numberOfTickets
     * @return $this
     */
    protected function createTickets($numberOfTickets)
    {
        $this->tickets = factory(Ticket::class, $numberOfTickets)->create();

        return $this;
    }

    /**
     * Create a batch of agents.
     * @param  int $numberOfUsers
     * @return $this
     */
    protected function createAgents($numberOfAgents)
    {
        $this->agents = factory(Agent::class, $numberOfAgents)->create();

        return $this;
    }

    /**
     * Create a batch of teams.
     * @param  int $numberOfTeams
     * @return $this
     */
    protected function createTeams($numberOfTeams)
    {
        $this->teams = factory(Team::class, $numberOfTeams)->create();

        return $this;
    }

    /**
     * Assign every other ticket to a random agent.
     * @return $this
     */
    protected function assignTicketsToUsers()
    {
        $this->tickets->each(function ($item, $key) {
            if ($key % 2 === 0) {
                $item->assignToAgent($this->agents->random());
            }
        });

        return $this;
    }

    /**
     * Assign half the unassigned tickets to assignment teams.
     * @return $this
     */
    protected function assignTicketsToTeams()
    {
        Ticket::unassigned()->get()->each(function ($item, $key) {
            if ($key % 2 === 0) {
                $item->assignToTeam($this->teams->random());
            }
        });

        return $this;
    }

    /**
     * Add a due date for each assigned or teamed tickets.
     * @return $this
     */
    protected function addDueDatesToAssignedOrTeamedTickets()
    {
        Ticket::has('assignment')->orHas('teamAssignment')->get()->each(function ($item) {
            $days = rand(-5, 5);

            $item->dueOn(Carbon::parse('now')->addDays($days));
        });

        return $this;
    }

    /**
     * Add internal replies to some assigned tickets.
     * @param int $numberOfReplies
     * @return $this
     */
    protected function addInternalRepliesToAssignedTickets($numberOfReplies)
    {
        Ticket::assigned()->take($numberOfReplies)->get()->each(function ($item) {
            $item->internalReply('This is a test reply from the seeder.', $item->assignment->assignee);
        });

        return $this;
    }

    /**
     * Close some random tickets.
     * @param  int $numberOfTickets
     * @return $this
     */
    protected function closeRandomTickets($numberOfTickets)
    {
        Ticket::inRandomOrder()->take($numberOfTickets)->get()->each(function ($item) {
            $item->close(null, $item->user);
        });

        return $this;
    }
}
