<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\Payment::class, function (Faker $faker) {
    return [
        'amount' => 15,
        'order_id' => factory(\App\Order::class)->create()->getKey(),
    ];
});
