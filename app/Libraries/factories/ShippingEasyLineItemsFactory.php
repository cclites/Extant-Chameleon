<?php
namespace App\Libraries\factories;

use Faker\Factory;


class ShippingEasyLineItemsFactory{

    /*
     * Valid values for orderStatus:
     * awaiting_payment, awaiting_shipment, shipped, on_hold, cancelled
     *
     * https://www.shipstation.com/docs/api/orders/create-update-order/
     */

    public static function create()
    {
        $faker = Factory::create();

        return [
            'ext_line_item_id' => $faker->randomLetter . "-" . $faker->randomNumber(4),
            'sku' => $faker->domainWord,
            'item_name' => $faker->name(),
            'quantity' => $faker->randomNumber(3),
            'unit_price' => $faker->randomFloat(3, 1, 4),
        ];

    }
}
