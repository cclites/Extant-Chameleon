<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;


class ControlPadResource extends JsonResource
{
    public function toArray($order)
    {
        if(is_array($order)){
            return  $order;
        }

        return $order->toArray();
    }

    public static function transformCPAddressToSSAddress($cpAddress)
    {
        return [
            'name' => $cpAddress->name,
            'street1' => $cpAddress->line_1,
            'street2' => $cpAddress->line_2,
            'city' => $cpAddress->city,
            'state' => $cpAddress->state,
            'postalCode' => $cpAddress->zip,
            'country' => 'US' //TODO: If users from other countries come on board, this will
                              //  need to be changed to a variable.
        ];
    }

    public static function transformCPOrderToSSOrder($order)
    {
        return [
            'orderNumber' => $order->pid,
            'orderKey' => $order->receipt_id,
            'orderDate' => $order->created_at,
            'orderStatus' => 'awaiting_shipment',
            'billTo' => self::transformCPAddressToSSAddress($order->billing_address),
            'shipTo' => self::transformCPAddressToSSAddress($order->shipping_address),
            'customerUsername' => $order->buyer_first_name . " " . $order->buyer_last_name,
        ];
    }
}
