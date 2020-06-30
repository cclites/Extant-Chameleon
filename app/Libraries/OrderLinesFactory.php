<?php
namespace App\Libraries;

use Faker\Factory;

class OrderLinesFactory{

    public static function create()
    {

        $faker = Factory::create();

        return [
            'lineItemKey' => $faker->word . "-" . $faker->randomNumber(2),
            'sku' => $faker->password(9),
            'name' => $faker->name(),
            'quantity' => $faker->randomNumber(4),
            'unitPrice' => number_format($faker->randomNumber(4), 2),
            'createDate' => $faker->date(),
        ];
    }


}

