<?php

namespace App\Transformers;


use App\Http\Resources\ControlPadResource;

class CpToSsTransformer
{
    /**
     * @param $order
     * @return array
     */
    public static function transform(array $order): array
    {
        if(!$order['lines']){
            \Log::error('Order has no lines');
            \Log::info(json_encode($order));
            die();
        }

        $customerUserName = $order['buyer_first_name'] . " " . $order['buyer_last_name'];

        $items = ControlPadResource::transformCPOrderItemToSSOrderItem($order['lines'], $customerUserName);

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

    }
}
