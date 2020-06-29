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

    public static function transformCPAddressToSSAddress(array $cpAddress, string $customerName): array
    {
        return [
            'name' => $customerName,
            'street1' => $cpAddress['line_1'],
            'street2' => $cpAddress['line_2'],
            'city' => $cpAddress['city'],
            'state' => $cpAddress['state'],
            'postalCode' => $cpAddress['zip'],
            'country' => 'US' //TODO: If users from other countries come on board, this will
                              //  need to be changed to a variable.
        ];
    }

    /**
     * @param array $order
     * @return array
     */
    public static function transformCPOrderToSSOrder(array $order): array
    {
        $items = [];
        $customerUserName = $order['buyer_first_name'] . " " . $order['buyer_last_name'];

        if( array_key_exists('lines', $order)){
            $items = collect($order['lines'])->map(function($line) use($customerUserName){
                return self::transformCPOrderItemToSSOrderItem(collect($line)->toArray(), $customerUserName);
            });
        }

        return [
            'orderNumber' => $order['id'],
            'orderKey' => $order['receipt_id'],
            'orderDate' => $order['created_at'],
            'orderStatus' => 'awaiting_shipment',
            'orderTotal' => $order['total_price'],
            'taxAmount' => $order['total_tax'],
            'amountPaid' => 0, //TODO:: Figure out which parameter to use here
            'shippingAmount' => $order['total_shipping'],
            'billTo' => self::transformCPAddressToSSAddress((array)$order['billing_address'], $customerUserName),
            'shipTo' => self::transformCPAddressToSSAddress((array)$order['shipping_address'], $customerUserName),
            'customerUsername' => $customerUserName,
            'items' => $items
        ];
    }

    public static function transformCPOrderItemToSSOrderItem(array $orderItem): array
    {
        return [
            'lineItemKey' => $orderItem['id'],
            'sku' => $orderItem['manufacturer_sku'],
            'name' => $orderItem['name'],
            'quantity' => $orderItem['quantity'],
            'unitPrice' => $orderItem['price'],
            //'taxAmount' => $orderItem->total_tax,
            //'shippingAmount' => $orderItem->total_shipping,
            'createDate' => $orderItem['created_at'],
        ];

    }

    public static function createTracking($tracking)
    {

    }
}
