<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(\App\Models\Permission::class, function (Faker $faker) {
    return [
        'parent_id' => 0,
        'name' => $faker->colorName,
        'action' => $faker->lexify(),
    ];
});
