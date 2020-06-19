<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Order;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(Order::class, function (Faker $faker) {

    return [
        'orderNumber' => $faker->randomNumber(12),
        'orderDate' => \Carbon\Carbon::now()->format('m/d/Y'),
        'orderStatus' => $faker->randomElement(['awaiting_payment', 'awaiting_shipment', 'shipped', 'on_hold', 'cancelled']),
        'billTo' => $faker->address,
        'shipTo' => $faker->address
    ];
});
