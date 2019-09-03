<?php

use Aviator\Helpdesk\Models\Action;
use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Assignment;
use Aviator\Helpdesk\Models\Closing;
use Aviator\Helpdesk\Models\Collaborator;
use Aviator\Helpdesk\Models\DueDate;
use Aviator\Helpdesk\Models\GenericContent;
use Aviator\Helpdesk\Models\Note;
use Aviator\Helpdesk\Models\Opening;
use Aviator\Helpdesk\Models\Reply;
use Aviator\Helpdesk\Models\Team;
use Aviator\Helpdesk\Models\TeamAssignment;
use Aviator\Helpdesk\Models\Ticket;
use Carbon\Carbon;

/*
 * Helpdesk factory facilities
 */

$factory->define(Action::class, function (Faker\Generator $faker) {
    return [
        'name' => 'Test Name',
        'subject_id' => factory(Ticket::class)->create()->id,
        'subject_type' => 'Aviator\Helpdesk\Models\Ticket',
        'object_id' => factory(Assignment::class)->create()->id,
        'object_type' => 'Aviator\Helpdesk\Models\Assignment',
    ];
});

$factory->define(Ticket::class, function (Faker\Generator $faker) {
    return [
        'user_id' => factory(config('helpdesk.userModel'))->create()->id,
        'content_id' => factory(GenericContent::class)->create()->id,
        'content_type' => 'Aviator\Helpdesk\Models\GenericContent',
        'status' => 'open',
        'uuid' => strtolower(str_random(32)),
        'is_ignored' => null,
    ];
});

$factory->define(GenericContent::class, function (Faker\Generator $faker) {
    return [
        'title' => $faker->sentence(2, true),
        'body' => $faker->paragraph(4, true),
    ];
});

$factory->define(Assignment::class, function (Faker\Generator $faker) {
    return [
        'ticket_id' => factory(Ticket::class)->create()->id,
        'assigned_to' => factory(Agent::class)->create()->id,
        'agent_id' => null,
        'is_visible' => false,
    ];
});

$factory->define(DueDate::class, function (Faker\Generator $faker) {
    return [
        'ticket_id' => factory(Ticket::class)->create()->id,
        'due_on' => Carbon::parse('+1 day'),
        'agent_id' => null,
        'is_visible' => false,
    ];
});

$factory->define(Reply::class, function (Faker\Generator $faker) {
    return [
        'ticket_id' => factory(Ticket::class)->create()->id,
        'body' => $faker->paragraph(2),
        'agent_id' => factory(Agent::class)->create()->id,
        'user_id' => null,
        'is_visible' => true,
    ];
});

$factory->state(Reply::class, 'isUser', function (Faker\Generator $faker) {
    return [
        'agent_id' => null,
        'user_id' => factory(config('helpdesk.userModel'))->create(),
    ];
});

$factory->define(Team::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->jobTitle(),
    ];
});

$factory->define(TeamAssignment::class, function (Faker\Generator $faker) {
    return [
        'ticket_id' => factory(Ticket::class)->create()->id,
        'team_id' => factory(Team::class)->create()->id,
        'agent_id' => null,
        'is_visible' => false,
    ];
});

$factory->define(Closing::class, function (Faker\Generator $faker) {
    return [
        'ticket_id' => factory(Ticket::class)->create([
            'status' => 'closed',
        ])->id,
        'note' => 'Test note',
        'agent_id' => factory(Agent::class)->create()->id,
        'is_visible' => true,
    ];
});

$factory->state(Closing::class, 'isUser', function (Faker\Generator $faker) {
    return [
        'user_id' => factory(config('helpdesk.userModel'))->create()->id,
        'is_visible' => true,
    ];
});

$factory->define(Opening::class, function (Faker\Generator $faker) {
    return [
        'ticket_id' => factory(Ticket::class)->create()->id,
        'agent_id' => null,
        'user_id' => factory(config('helpdesk.userModel'))->create()->id,
        'is_visible' => true,
    ];
});

$factory->state(Opening::class, 'isAgent', function (Faker\Generator $faker) {
    return [
        'agent_id' => factory(Agent::class)->create()->id,
        'user_id' => null,
    ];
});

$factory->define(Note::class, function (Faker\Generator $faker) {
    return [
        'ticket_id' => factory(Ticket::class)->create()->id,
        'body' => $faker->paragraph(2),
        'agent_id' => factory(Agent::class)->create()->id,
        'user_id' => null,
        'is_visible' => false,
    ];
});

$factory->state(Note::class, 'isUser', function (Faker\Generator $faker) {
    return [
        'agent_id' => null,
        'user_id' => factory(config('helpdesk.userModel'))->create()->id,
    ];
});

$factory->define(Collaborator::class, function (Faker\Generator $faker) {
    $agent = factory(Agent::class)->create();

    return [
        'agent_id' => factory(Agent::class)->create(),
        'ticket_id' => factory(Ticket::class)->create()->id,
        'created_by' => factory(Agent::class)->create(),
    ];
});
