<?php

namespace Aviator\Helpdesk\Database\Factories;

use Aviator\Helpdesk\Models\Team;
use Aviator\Helpdesk\Models\TeamAssignment;
use Aviator\Helpdesk\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeamAssignmentFactory extends Factory
{
    protected $model = TeamAssignment::class;

    public function definition(): array
    {
        return [
            'ticket_id' => Ticket::factory(),
            'team_id' => Team::factory(),
            'agent_id' => null,
            'is_visible' => false,
        ];
    }
}
