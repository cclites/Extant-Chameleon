<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Address;
use Faker\Factory;
//use Faker\Generator as Faker;

class AddressFactory{

    public static function ssCreate(){

        $faker = Factory::create();

        return [
            'name' => $faker->firstName . " " . $faker->lastName,
            'street1'=> $faker->streetAddress,
            'street2'=> $faker->secondaryAddress,
            'city'=> $faker->city,
            'state'=>$faker->state,
            'zip' => $faker->postcode,
        ];

    }

    public static function cpCreate()
    {
        $faker = Factory::create();

        return [
            'line_1'=> $faker->streetAddress,
            'line_2'=> $faker->secondaryAddress,
            'city'=> $faker->city,
            'state'=>$faker->state,
            'zip' => $faker->postcode,
        ];
    }

}



/*
$factory->define(Address::class, function (Faker $faker) {

    $types = ['Primary', 'Secondary', 'Billing'];


    $typeIndex = array_rand($types);
    $type = $types[$typeIndex];

    return [
        'address_1'=> $faker->streetAddress,
        'address_2'=> $faker->secondaryAddress,
        'city'=> $faker->city,
        'state'=>$faker->state,
        'zip' => $faker->postcode,
        'type'=> $type
    ];
});
*/
