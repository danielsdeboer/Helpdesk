<?php

$factory->define(config('helpdesk.userModel'), function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email,
    ];
});

$factory->state(config('helpdesk.userModel'), 'isInternal', function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email,
        'is_internal' => 1,
    ];
});
