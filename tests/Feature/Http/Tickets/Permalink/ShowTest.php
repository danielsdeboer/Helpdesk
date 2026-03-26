<?php

namespace Aviator\Helpdesk\Tests\Feature\Http\Tickets\Permalink;

use Aviator\Helpdesk\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ShowTest extends TestCase
{
    /** @var string */
    protected $url = 'helpdesk/tickets/permalink/';

    /**
     * @param null $id
     */
    protected function url($id = null): string
    {
        return $this->url . ($id ?: 1);
    }

    #[Test]
    public function guests_may_visit()
    {
        $ticket = $this->make->ticket;

        $response = $this->get($this->url($ticket->permalink));
        $response->assertSuccessful();
    }

    #[Test]
    public function guests_do_not_see_the_action_bar()
    {
        $ticket = $this->make->ticket;

        $response = $this->get($this->url($ticket->permalink));

        $response->assertSuccessful()
            ->assertDontSee('id="ticket-toolbar"');
    }
}
