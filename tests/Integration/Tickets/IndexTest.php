<?php

namespace Aviator\Helpdesk\Tests\Integration\Tickets;

use Aviator\Helpdesk\Tests\TestCase;

class IndexTest extends TestCase
{
    /** @var string */
    protected $url = 'helpdesk/tickets';

    /** @test */
    public function guests_are_redirected_to_login ()
    {
        $response = $this->get($this->url);

        $response->assertStatus(302)
            ->assertRedirect('login');
    }

    /** @test */
    public function agents_may_visit ()
    {
        $this->be($this->make->agent->user);

        $this->withoutErrorHandling();
        $response = $this->get($this->url);

        $response->assertSuccessful();
    }

    /** @test */
    public function users_can_visit ()
    {
        $this->be($this->make->user);

        $response = $this->get($this->url);

        $response->assertStatus(200);
    }

    /** @test */
    public function users_only_see_their_own_open_and_closed_tickets ()
    {
        $user = $this->make->user;
        $openTicket = $this->make->ticket($user);
        $closedTicket = $this->make->ticket($user)->close(null, $user);
        $otherOpenTicket = $this->make->ticket;
        $otherClosedTicket = $this->make->ticket->close(null, $user);

        $this->be($user);

        $response = $this->get($this->url);

        $response->data('open')->assertContains($openTicket);
        $response->data('open')->assertNotContains($otherOpenTicket);
        $response->data('closed')->assertContains($closedTicket);
        $response->data('closed')->assertNotContains($otherClosedTicket);
    }

    /** @test */
    public function agents_see_tickets_assigned_to_them ()
    {
        $agent = $this->make->agent;
        $ticket1 = $this->make->ticket->assignToAgent($agent);
        $ticket2 = $this->make->ticket->assignToAgent($agent)->close('note', $agent);
        $ticket3 = $this->make->ticket;
        $ticket4 = $this->make->ticket->close('note', $agent);

        $this->be($agent->user);

        $response = $this->get($this->url);

        $response->data('open')->assertContains($ticket1);
        $response->data('closed')->assertContains($ticket2);
        $response->data('open')->assertNotContains($ticket3);
        $response->data('closed')->assertNotContains($ticket4);
    }

    /** @test */
    public function only_the_header_tickets_tab_is_enabled ()
    {
        $this->withoutErrorHandling();
        $user = $this->make->user;

        $this->be($user);
        $response = $this->get($this->url);

        $response->assertActiveHeaderTab('tickets');
    }

    /** @test */
    public function for_less_than_24_tickets_the_see_more_button_is_disabled ()
    {
        $user = $this->make->user;
        $this->make->ticket($user);

        $this->be($user);

        $response = $this->get($this->url);

        $response->assertSee('<a id="open-see-more" class="button is-disabled">No more to show...</a>');
    }

    /** @test */
    public function for_more_than_24_tickets_the_see_more_button_is_enabled ()
    {
        $this->withoutErrorHandling();
        $user = $this->make->user;
        $this->make->tickets(25, $user);

        $this->be($user);
        $response = $this->get($this->url);

        $response->assertSee('<a id="open-see-more" class="button" href=');
    }

    /** @test */
    public function ignored_tickets_only_appear_in_ignored_list_for_supers ()
    {
        $user1 = $this->make->user;
        $user2 = $this->make->user;
        $ignoredUser = $this->make->user;
        $super = $this->make->super;

        $this->addIgnoredUser([$ignoredUser->email]);

        $user1OpenTicket = $this->make->ticket($user1);
        $user2OpenTicket = $this->make->ticket($user2);
        $user1ClosedTicket = $user1OpenTicket->close(null, $user1);
        $ignoredOpenTicket = $this->make->ticket($ignoredUser);
        $ignoredClosedTicket = $ignoredOpenTicket->close(null, $ignoredUser);

        $response = $this->actingAs($super->user)->get($this->url);

        $htmlString = $response->getContent();

        $this->assertEquals(1, substr_count($htmlString, $ignoredUser->name));
    }

    /** @test */
    public function ignored_users_see_open_and_closed_tickets ()
    {
        $user1 = $this->make->user;
        $user2 = $this->make->user;
        $ignoredUser1 = $this->make->user;
        $ignoredUser2 = $this->make->user;
        $super = $this->make->super;

        $this->addIgnoredUser([$ignoredUser1->email, $ignoredUser2->email]);

        $ignoredOpenTicket1 = $this->make->ticket($ignoredUser1);
        $ignoredOpenTicket2 = $this->make->ticket($ignoredUser2);
        $ignoredClosedTicket1 = $ignoredOpenTicket1->close(null, $ignoredUser1);

        $response = $this->actingAs($ignoredUser1)->get($this->url);

        $htmlString = $response->getContent();
        
        $response->assertDontSee('<a id="ignored-see-more"');
        $this->assertEquals(1, substr_count($htmlString, $ignoredUser1->name));
    }
}
