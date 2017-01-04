<?php

namespace Aviator\Helpdesk\Database\Seeders;

use Aviator\Helpdesk\Models\Pool;
use Aviator\Helpdesk\Models\Ticket;
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
            ->assignTicketsToPools();
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
}
