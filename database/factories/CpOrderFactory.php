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

$factory->define(Order::class, function (Faker $faker)
{
    return [
        "receipt_id" => $faker->randomLetter . "-" . $faker->randomNumber(4),
        'buyer_first_name' => $faker->firstName,
        'buyer_last_name'=> $faker->lastName,
        'status' => 'unfulfilled',
        'created_at' => \Carbon\Carbon::now(),
        'billing_address' => AddressFactory::cpCreate(),
        'shipping_address' => AddressFactory::cpCreate(),
    ];
});
