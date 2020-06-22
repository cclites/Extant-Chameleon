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
        'orderKey' => $faker->randomLetter . "-" . $faker->randomNumber(4),
        'orderNumber' => $faker->randomNumber(12),
        'orderDate' => \Carbon\Carbon::now()->format('m/d/Y'),
        'orderStatus' => 'pending',
        'billTo' => AddressFactory::ssCreate(),
        'shipTo' => AddressFactory::ssCreate(),
    ];
});
