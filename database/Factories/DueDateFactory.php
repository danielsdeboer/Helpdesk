<?php

namespace Aviator\Helpdesk\Database\Factories;

use Aviator\Helpdesk\Models\DueDate;
use Aviator\Helpdesk\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class DueDateFactory extends Factory
{
    protected $model = DueDate::class;

    public function definition(): array
    {
        return [
            'ticket_id' => Ticket::factory(),
            'due_on' => Carbon::parse('+1 day'),
            'agent_id' => null,
            'is_visible' => false,
        ];
    }
}
