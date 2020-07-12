<?php

namespace App\Libraries;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use Faker\Factory;


class TrackingFactory{

    public static function create(object $order)
    {
        $faker = Factory::create();

        return [
            'order_id' => $order->id,
            'number' => $order->pid,
            'shipped_at' => \Carbon\Carbon::now()
        ];
    }

}

