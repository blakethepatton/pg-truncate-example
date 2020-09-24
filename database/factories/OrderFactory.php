<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\Order::class, function (Faker $faker) {
    return [
        'product_id' => \App\Product::query()->inRandomOrder()->first()->getKey(),
        'user_id' => \App\User::query()->inRandomOrder()->first()->getKey(),
    ];
});
