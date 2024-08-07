<?php

use Faker\Generator;

$factory->define(config('helpdesk.userModel'), function (Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email,
    ];
});

$factory->state(config('helpdesk.userModel'), 'isInternal', function (Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email,
        'is_internal' => 1,
    ];
});
