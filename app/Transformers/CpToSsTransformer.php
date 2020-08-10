<?php

namespace App\Transformers;


use App\Http\Resources\ControlPadResource;

class CpToSsTransformer
{
    /**
     * @param $orders
     * @return array
     */
    public static function transform($order): array
    {
        $data = [];

        //dump($orders);
        //die("\n");

        //foreach($orders as $order){




            $customerUserName = $order['buyer_first_name'] . " " . $order['buyer_last_name'];

            if( !array_key_exists('lines', $order)){
                \Log::error('Order has no items');
                \Log::info(json_encode($order));
                die();
            }

            $items = collect($order['lines'])->map(function($line) use($customerUserName){
                return ControlPadResource::transformCPOrderItemToSSOrderItem($line, $customerUserName);
            });

            return  [
                'orderNumber' => $order['id'],
                'orderKey' => $order['receipt_id'],
                'orderDate' => $order['created_at'],
                'orderStatus' => 'awaiting_shipment',
                'orderTotal' => $order['total_price'],
                'taxAmount' => $order['total_tax'],
                'amountPaid' => 0, //TODO:: Figure out which parameter to use here
                'shippingAmount' => $order['total_shipping'],
                'billTo' => ControlPadResource::transformCPAddressToSSAddress((array)$order['billing_address'], $customerUserName),
                'shipTo' => ControlPadResource::transformCPAddressToSSAddress((array)$order['shipping_address'], $customerUserName),
                'customerUsername' => $customerUserName,
                'items' => $items
            ];


        //}


    }
}
