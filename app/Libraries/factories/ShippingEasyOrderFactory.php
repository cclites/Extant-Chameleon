<?php
namespace App\Libraries\factories;

use Faker\Factory;

class ShippingEasyOrderFactory{

    /*
     * Valid values for orderStatus:
     * awaiting_payment, awaiting_shipment, shipped, on_hold, cancelled
     *
     * https://shippingeasy.readme.io/docs/apiorders
     */

    public static function create()
    {
        $faker = Factory::create();
        $recipients = ShippingEasyRecipientFactory::create();

        $data =  [
            'external_order_identifier' => $faker->randomLetter . "-" . $faker->randomNumber(4),
            'alternate_order_id' => $faker->randomNumber(3),
            'ordered_at' => \Carbon\Carbon::now()->format('m/d/Y'),
            'order_status' => 'awaiting_shipment',
            'total_including_tax' => 125.25,
            'total_tax' => 0,
            'shipping_cost_including_tax' => 0,
            'billing_first_name' => $faker->name,
            'billing_last_name' => $faker->lastName,
            'billing_address' => $faker->streetAddress,
            'billing_address_2' => $faker->secondaryAddress,
            'billing_city' => $faker->city,
            'billing_state' => $faker->state,
            'billing_postal_code' => $faker->postcode,
            'billing_phone_number' => $faker->phoneNumber,
            'billing_country' => 'US', //TODO: If users from other countries come on board, this will need to be changed to a field.
            'recipients' => [$recipients],
        ];

        //dd($data);
        return $data;
    }
}
