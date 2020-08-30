<?php

namespace App\Transformers;


use App\Http\Resources\ControlPadResource;
use Illuminate\Support\Arr;

class CpBuyerToSeRecipients
{
    public static function transform(array $order): array
    {

        if(!$lines = $order['lines']) {
            \Log::error("CpBuyerToSeRecipients:ERROR. There must be order lines.");
            //\Log::info(json_encode($order));
            die("CpBuyerToSeRecipients:ERROR. There must be order lines.");
        }

        $lineItems = ControlPadResource::transformCPOrderItemToSEOrderItem($lines);

        /**
         * This is a workaround to handle the difference between a test address
         * and an actual order from CP. TestStub addresses always come back as an array,
         * CP addresses are parsed as objects.
         */
        //if(is_object($order['shipping_address'])){ // Is CP address object
          //  $shippingAddress = (array)$order['shipping_address'];
        //}else{ // Is test address object
          //  $shippingAddress = $order['shipping_address'];
        //}

        $address2 = array_key_exists('line_2', $order['shipping_address']) ? $order['shipping_address']['line_2'] : '';

        $data = [
            'company' => $order['buyer_first_name'] . " " . $order['buyer_last_name'],
            'first_name' => $order['buyer_first_name'],
            'last_name' => $order['buyer_last_name'],
            'address' => $order['shipping_address']['line_1'],
            'address2' => $address2,
            'city' => $order['shipping_address']['city'],
            'state' => $order['shipping_address']['state'],
            'postal_code' => $order['shipping_address']['zip'],
            'line_items' => $lineItems
        ];

        /*************************************************************/
        return $data;

    }
}
