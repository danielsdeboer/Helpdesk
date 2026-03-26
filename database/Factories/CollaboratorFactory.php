<?php

namespace Aviator\Helpdesk\Database\Factories;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Collaborator;
use Aviator\Helpdesk\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

class CollaboratorFactory extends Factory
{
    protected $model = Collaborator::class;

    public function definition(): array
    {
        return [
            'agent_id' => Agent::factory(),
            'ticket_id' => Ticket::factory(),
            'created_by' => Agent::factory(),
        ];
    }
}
