<?php

namespace App\Transformers;


use App\Http\Resources\ControlPadResource;
use Illuminate\Support\Arr;

class CpBuyerToSeRecipients
{
    public static function transform($order): array
    {
        $lineItems = ControlPadResource::transformCPOrderItemToSEOrderItem($order['lines']);

        if(is_object($order['shipping_address'])){
            $shippingAddress = (array)$order['shipping_address'];
        }else{
            $shippingAddress = $order['shipping_address'][0];
        }

        $address2 = '';

        if(array_key_exists('line_2', $shippingAddress)){
            $address2 = $shippingAddress['line_2'];
        }

        return [
            'company' => $order['buyer_first_name'] . " " . $order['buyer_last_name'],
            'first_name' => $order['buyer_first_name'],
            'last_name' => $order['buyer_last_name'],
            'address' => $shippingAddress['line_1'],
            'address2' => $address2,
            'city' => $shippingAddress['city'],
            'state' => $shippingAddress['state'],
            'postal_code' => $shippingAddress['zip'],
            'line_items' => $lineItems
        ];

    }
}
