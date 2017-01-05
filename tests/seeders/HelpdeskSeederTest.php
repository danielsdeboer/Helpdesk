<?php

namespace Aviator\Helpdesk\Tests\Seeders;

use Aviator\Helpdesk\Database\Seeds\HelpdeskSeeder;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Tests\TestCase;
use Aviator\Helpdesk\Tests\User;

class HelpdeskSeederTest extends TestCase {

    /**
     * @group seeders
     * @test
     */
    public function it_creates_tickets()
    {
        $this->seed(HelpdeskSeeder::class);

        $seededTickets = Ticket::all();

        $this->assertEquals(52, $seededTickets->count());
    }

    /**
     * @group seeders
     * @test
     */
    public function it_assigns_half_the_tickets_to_pools()
    {
        $this->seed(HelpdeskSeeder::class);

        $assignedTickets = Ticket::assigned()->get();

        $this->assertEquals(26, $assignedTickets->count());
    }

    /**
     * @group seeders
     * @test
     */
    public function it_assigns_half_the_remaining_unassigned_tickets_to_assignment_pools()
    {
        $this->seed(HelpdeskSeeder::class);

        $tickets = Ticket::pooled()->get();

        $this->assertEquals(13, $tickets->count());
    }

    /**
     * @group seeders
     * @test
     */
    public function it_adds_a_due_date_for_assigned_and_pooled_tickets()
    {
        $this->seed(HelpdeskSeeder::class);

        $tickets = Ticket::has('dueDate')->get();

        $this->assertEquals(39, $tickets->count());
    }

    /**
     * @group seeders
     * @test
     */
    public function it_adds_a_reply_to_a_subset_of_assigned_tickets()
    {
        $this->seed(HelpdeskSeeder::class);

        $tickets = Ticket::has('internalReplies')->get();

        $this->assertEquals(5, $tickets->count());
    }

    /**
     * @group seeders
     * @test
     */
    public function it_closes_some_random_tickets()
    {
        $this->seed(HelpdeskSeeder::class);

        $tickets = Ticket::has('closings')->get();

        $this->assertEquals(10, $tickets->count());
    }
}