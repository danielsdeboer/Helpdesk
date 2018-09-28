<?php

namespace Aviator\Helpdesk\Tests\Integration\Tickets\Ignored;

use Carbon\Carbon;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Tests\TestCase;
use Illuminate\Support\Facades\Config;

class IndexTest extends TestCase
{
    /** @var string */
    protected $url = 'helpdesk/tickets/ignored';

    /** @test */
    public function guests_are_redirected_to_login ()
    {
        $response = $this->get($this->url);

        $response->assertStatus(302)
            ->assertRedirect('login');
    }

    /** @test */
    public function only_supers_see_ignored_tickets ()
    {
        $this->withoutErrorHandling();
        $user = $this->make->user;
        $agent = $this->make->agent;
        $ignoredUser = $this->make->user;
        $super = $this->make->super;

        Config::set('helpdesk.ignored', [
            $ignoredUser->email,
        ]);

        $ticket1 = $this->make->ticket($user);
        $ticket2 = $this->make->ticket($ignoredUser);

        $response = $this->actingAs($super->user)->get($this->url);
        $response->assertStatus(200);
        $response->data('ignored')->assertNotContains($ticket1);
        $response->data('ignored')->assertContains($ticket2);

        $response = $this->actingAs($agent->user)->get($this->url);
        $response->assertStatus(200);
        $response->assertSee('<p>Nothing to see here.</p>');
    }

    /** @test */
    public function results_are_paginated_when_displaying_more_than_24_tickets ()
    {
        $this->withoutErrorHandling();
        $user = $this->make->user;
        $ignoredUser = $this->make->user;
        $super = $this->make->super;

        Config::set('helpdesk.ignored', [
            $ignoredUser->email,
        ]);

        $ticket = $this->make->ticket($ignoredUser);

        $response = $this->actingAs($super->user)->get($this->url);

        // We have no pagination here since there are too few results.
        $response->assertStatus(200);
        $response->assertDontSee('nav class="pagination"');

        $this->make->tickets(24, $ignoredUser);

        $response = $this->actingAs($super->user)->get($this->url);

        // We have pagination due to the number of results.
        $response->assertStatus(200);
        $response->assertSee('<ul class="pagination-list">');
    }

    /** @test */
    public function results_are_ordered_by_latest_first ()
    {
        $ignoredUser = $this->make->user;
        $super = $this->make->super;

        Config::set('helpdesk.ignored', [
            $ignoredUser->email,
        ]);

        $ticket1 = $this->make->ticket($ignoredUser);
        $ticket2 = $this->make->ticket($ignoredUser);
        $ticket3 = $this->make->ticket($ignoredUser);

        $ticket1->created_at = Carbon::parse('2 years ago');
        $ticket1->save();
        $ticket2->created_at = Carbon::parse('yesterday');
        $ticket2->save();
        $ticket3->created_at = Carbon::now();
        $ticket3->save();

        $response = $this->actingAs($super->user)->get($this->url);

        $response->assertStatus(200);
        $this->assertSame($ticket3->id, $response->data('ignored')[0]->id);
        $this->assertSame($ticket2->id, $response->data('ignored')[1]->id);
        $this->assertSame($ticket1->id, $response->data('ignored')[2]->id);
    }
}
