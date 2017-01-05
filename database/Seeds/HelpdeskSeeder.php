<?php

namespace Aviator\Helpdesk\Database\Seeds;

use Aviator\Helpdesk\Models\Pool;
use Aviator\Helpdesk\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class HelpdeskSeeder extends Seeder
{
    protected $tickets;
    protected $users;
    protected $pools;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createTickets(52)
            ->createUsers(10)
            ->createAssignmentPools(3)
            ->assignTicketsToUsers()
            ->assignTicketsToPools()
            ->addDueDatesToAssignedOrPooledTickets()
            ->addInternalRepliesToAssignedTickets(5)
            ->closeRandomTickets(10);
    }

    /**
     * Create a batch of tickets
     * @param  int $numberOfTickets
     * @return $this
     */
    protected function createTickets($numberOfTickets)
    {
        $this->tickets = factory(Ticket::class, $numberOfTickets)->create();

        return $this;
    }

    /**
     * Create a batch of internal users
     * @param  int $numberOfUsers
     * @return $this
     */
    protected function createUsers($numberOfUsers)
    {
        $this->users = factory(config('helpdesk.userModel'), $numberOfUsers)->create();

        return $this;
    }

    protected function createAssignmentPools($numberOfPools)
    {
        $this->pools = factory(Pool::class, $numberOfPools)->create();

        return $this;
    }

    /**
     * Assign every other ticket to a random user
     * @return $this
     */
    protected function assignTicketsToUsers()
    {
        $this->tickets->each(function($item, $key) {
            if ($key % 2 === 0) {
                $item->assignToUser($this->users->random());
            }
        });

        return $this;
    }

    /**
     * Assign half the unassigned tickets to assignment pools
     * @return $this
     */
    protected function assignTicketsToPools()
    {
        Ticket::unassigned()->get()->each(function($item, $key) {
            if ($key % 2 === 0) {
                $item->assignToPool($this->pools->random());
            }
        });

        return $this;
    }

    /**
     * Add a due date for each assigned or pooled tickets
     * @return $this
     */
    protected function addDueDatesToAssignedOrPooledTickets()
    {
        Ticket::has('assignment')->orHas('poolAssignment')->get()->each(function($item) {
            $days = rand(-5, 5);

            $item->dueOn(Carbon::parse('now')->addDays($days));
        });

        return $this;
    }

    /**
     * Add internal replies to some assigned tickets
     * @param int $numberOfReplies
     * @return $this
     */
    protected function addInternalRepliesToAssignedTickets($numberOfReplies)
    {
        Ticket::assigned()->take($numberOfReplies)->get()->each(function($item) {
            $item->internalReply('This is a test reply from the seeder.', $item->assignment->assignee);
        });

        return $this;
    }

    /**
     * Close some random tickets
     * @param  int $numberOfTickets
     * @return $this
     */
    protected function closeRandomTickets($numberOfTickets)
    {
        Ticket::inRandomOrder()->take($numberOfTickets)->get()->each(function($item) {
            $item->close(null, $item->user);
        });

        return $this;
    }
}
