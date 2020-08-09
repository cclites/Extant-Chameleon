<?php
namespace App\Libraries\factories;

use Faker\Factory;

class ControlPadLineItemsFactory{

    public static function create()
    {
        $faker = Factory::create();

        $data = [
            'id' => '9999',
            'manufacturer_sku' => $faker->password(9),
            'name' => $faker->name(),
            'quantity' => $faker->randomNumber(3),
            'price' => number_format($faker->randomNumber(2), 2),
            'created_at' => $faker->date(),
        ];

        return [$data];
    }


}

