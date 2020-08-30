<?php

namespace App\Libraries\factories;

use Faker\Factory;

class CpOrderFactory{

    public static function create()
    {
        $faker = Factory::create();
        $lines = [];

        for($i=0; $i<1; $i += 1){
            $lines[] = ControlPadLineItemsFactory::create();
        }

        $data = [
            'id' => $faker->randomNumber(2),
            'total_price' => 55.55,
            'total_tax' => 5.55,
            'total_shipping' => 555.55,
            'receipt_id' => $faker->randomLetter . "-" . $faker->randomNumber(4),
            'buyer_first_name' => $faker->firstName,
            'buyer_last_name'=> $faker->lastName,
            'status' => 'unfulfilled',
            'created_at' => \Carbon\Carbon::now()->toDateTimeString(),
            'billing_address' => AddressFactory::cpCreate(),
            'shipping_address' => AddressFactory::cpCreate(),
            'lines' => $lines,
        ];

        return $data;
    }

}

