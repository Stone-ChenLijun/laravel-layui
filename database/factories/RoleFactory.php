<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(\App\Models\Role::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->colorName,
        'is_enable' => true,
        'sort' => $faker->numberBetween(0, 10),
    ];
});
