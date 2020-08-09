<?php

namespace App\Transformers;


use App\Http\Resources\ControlPadResource;

class CpBuyerToSeRecipients
{
    public static function transform($order): array
    {

        $lineItems = ControlPadResource::transformCPOrderItemToSEOrderItem($order['lines']);

        return [
            'recipients' => [
            'company' => $order['buyer_first_name'] . " " . $order['buyer_last_name'],
            'first_name' => $order['buyer_first_name'],
            'last_name' => $order['buyer_last_name'],
            'address' => $order['shipping_address']['line_1'],
            'address2' => $order['shipping_address']['line_2'],
            'city' => $order['shipping_address']['city'],
            'state' => $order['shipping_address']['state'],
            'postal_code' => $order['shipping_address']['zip'],
            'line_items' => $lineItems
            ],
        ];

    }
}
