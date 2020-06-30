<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use Faker\Factory;
use App\Libraries\AddressFactory;
use App\Libraries\OrderLinesFactory;

class SsOrderFactory{

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
            'orderKey' => $faker->randomLetter . "-" . $faker->randomNumber(4),
            'orderNumber' => $faker->randomNumber(3),
            'orderDate' => \Carbon\Carbon::now()->format('m/d/Y'),
            'orderStatus' => 'awaiting_shipment',
            'billTo' => AddressFactory::ssCreate(),
            'shipTo' => AddressFactory::ssCreate(),
        ];
    }
}
