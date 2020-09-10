<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(App\Models\Menu::class, function (Faker $faker) {
    return [
        'name' => $faker->colorName,
        'parent_id' => 0,
        'url' => '#',
        'permission_id' => 0,
        'icon' => 'layui-icon-list',
    ];
});
