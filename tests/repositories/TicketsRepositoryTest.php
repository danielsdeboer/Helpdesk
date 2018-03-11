<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Repositories\TicketsRepository;

class TicketsRepositoryTest extends TestCase
{
    /**
     * @return \Aviator\Helpdesk\Repositories\TicketsRepository
     */
    protected function repo () : TicketsRepository
    {
        return app(TicketsRepository::class);
    }

   /** @test */
   public function it_scopes_queries_to_the_user ()
   {
       $user = $this->make->user;

       // The ticket assigned to this user
       $this->make->ticket($user);

       // Some other ticket
       $this->make->ticket;

       $this->be($user);
       $repo = $this->repo();

       $this->assertCount(1, $repo->get());
   }

   /** @test */
   public function it_scopes_queries_to_the_agent ()
   {
       $agent = $this->make->agent;

       $this->make->ticket->assignToAgent($agent);
       $this->make->ticket;

       $this->be($agent->user);
       $repo = $this->repo();

       $this->assertCount(1, $repo->get());
   }

    /** @test */
    public function it_scopes_queries_to_the_super ()
    {
        $super = $this->make->super;

        $this->make->tickets(2);

        $this->be($super->user);
        $repo = $this->repo();

        $this->assertCount(2, $repo->get());
   }

    /**
     * @test
     * @throws \Aviator\Helpdesk\Exceptions\CreatorRequiredException
     */
   public function it_gets_open_tickets ()
   {
       $super = $this->make->super;

       $this->make->ticket;
       $this->make->ticket->close(null, $super);
       $this->make->ticket->close(null, $super);

       $this->be($super->user);
       $repo = $this->repo()->open();

       $this->assertCount(1, $repo->get());
   }

    /**
     * @test
     * @throws \Aviator\Helpdesk\Exceptions\CreatorRequiredException
     */
   public function it_gets_closed_tickets ()
   {
       $super = $this->make->super;

       $this->make->ticket;
       $this->make->ticket->close(null, $super);
       $this->make->ticket->close(null, $super);

       $this->be($super->user);
       $repo = $this->repo()->closed();

       $this->assertCount(2, $repo->get());
   }

   /** @test */
   public function it_gets_overdue_tickets ()
   {
       $super = $this->make->super;

       $this->make->ticket;
       $this->make->ticket->dueOn('10 days ago');
       $this->make->ticket->dueOn('1 year ago');

       $this->be($super->user);
       $repo = $this->repo()->overdue();

       $this->assertCount(2, $repo->get());
   }
}
