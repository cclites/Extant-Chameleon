<?php
namespace App\Libraries\factories;

use Faker\Factory;

class ShippingEasyRecipientFactory{

    public static function create()
    {
        $faker = Factory::create();

        //Add line items
        $lineItems = ShippingEasyLineItemsFactory::create();

        return [
            'company' => $faker->company,
            'first_name' => $faker->name,
            'last_name' => $faker->lastName,
            'address' => $faker->streetAddress,
            'address2' => $faker->secondaryAddress,
            'city' => $faker->city,
            'state' => $faker->state,
            'postal_code' => $faker->postcode,
            'email' => $faker->safeEmail,
            'line_items' => [$lineItems]
        ];

    }

}
