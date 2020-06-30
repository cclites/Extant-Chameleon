<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use Faker\Factory;

class CpOrderFactory{

    public static function create()
    {
        $faker = Factory::create();

        $lines = OrderLinesFactory::create();

        return [
            'id' => $faker->randomNumber(2),
            'total_price' => 55.55,
            'total_tax' => 5.55,
            'total_shipping' => 555.55,
            'receipt_id' => $faker->randomLetter . "-" . $faker->randomNumber(4),
            'buyer_first_name' => $faker->firstName,
            'buyer_last_name'=> $faker->lastName,
            'status' => 'unfulfilled',
            'created_at' => \Carbon\Carbon::now(),
            'billing_address' => AddressFactory::cpCreate(),
            'shipping_address' => AddressFactory::cpCreate(),
            'lines' => $lines,
        ];
    }

}

