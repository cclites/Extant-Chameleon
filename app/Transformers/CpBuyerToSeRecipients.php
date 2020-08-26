<?php

namespace App\Transformers;


use App\Http\Resources\ControlPadResource;
use Illuminate\Support\Arr;

class CpBuyerToSeRecipients
{
    public static function transform(array $order): array
    {

        $order = $order[0];

        if(!$lines = $order['lines']) {
            \Log::error("CpBuyerToSeRecipients:ERROR. There must be order lines.");
            //\Log::info(json_encode($order));
            die("CpBuyerToSeRecipients: No order lines");
        }

        $lineItems = ControlPadResource::transformCPOrderItemToSEOrderItem($lines);

        /**
         * This is a workaround to handle the difference between a test address
         * and an actual order from CP. Test addresses always come back as an array,
         * CP addresses are parsed as objects.
         */
        if(is_object($order['shipping_address'])){ // Is CP address object
            $shippingAddress = (array)$order['shipping_address'];
        }else{ // Is test address object
            $shippingAddress = $order['shipping_address'];
        }

        $address2 = array_key_exists('line_2', $shippingAddress) ? $shippingAddress['line_2'] : '';

        $data = [
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

        /*************************************************************/
        return $data;

    }
}
