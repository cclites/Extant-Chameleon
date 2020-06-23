<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use Faker\Factory;

class CpOrderFactory{

    public static function create()
    {
        $faker = Factory::create();

        return [
            "receipt_id" => $faker->randomLetter . "-" . $faker->randomNumber(4),
            'buyer_first_name' => $faker->firstName,
            'buyer_last_name'=> $faker->lastName,
            'status' => 'unfulfilled',
            'created_at' => \Carbon\Carbon::now(),
            'billing_address' => AddressFactory::cpCreate(),
            'shipping_address' => AddressFactory::cpCreate(),
        ];
    }


}

