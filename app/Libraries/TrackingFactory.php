<?php

namespace App\Libraries;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use Faker\Factory;


class TrackingFactory{

    public static function create(?object $order = null)
    {
        $faker = Factory::create();

        return [
            'order_id' => $order ? $order->pid : $faker->randomNumber(9),
            'number' => $order ? $order->pid : $faker->randomNumber(4),
            'url' => 'http://fakeurl.com',
            'shipped_at' => \Carbon\Carbon::now()
        ];
    }

}

