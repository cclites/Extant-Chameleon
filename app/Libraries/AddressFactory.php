<?php

namespace App\Libraries;

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Factory;

class AddressFactory{

    public static function ssCreate(){

        $faker = Factory::create();

        return [
            'name' => $faker->firstName . " " . $faker->lastName,
            'street1'=> $faker->streetAddress,
            'street2'=> $faker->secondaryAddress,
            'city'=> $faker->city,
            'state'=>$faker->state,
            'postalCode' => $faker->postcode,
            'country' => 'US'
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

    /*
    public static function createAddress(): array
    {
        $faker = Faker::create();

        return [
            'name' => $faker->name,
            'company' => $faker->company,
            'street1'=> $faker->streetAddress,
            'street2'=> $faker->secondaryAddress,
            'street3' => '',
            'city'=> $faker->city,
            'state'=>$faker->state,
            'postalCode' => $faker->postcode,
            'country' => 'US',
            'phone' => $faker->phoneNumber,
        ];
    }*/
}
