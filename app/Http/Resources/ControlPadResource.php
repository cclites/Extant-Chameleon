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

    /**
     * @param array $cpAddress
     * @param string $customerName
     * @return array
     */
    public static function transformCPAddressToSSAddress(array $cpAddress, string $customerName): array
    {
        // If debugging, the address will already be formatted correctly.
        // This is a flaw.
        if(env('APP_DEBUG')){
            return $cpAddress;
        }

        return [
            'name' => $customerName,
            'street1' => $cpAddress['line_1'],
            'street2' => !empty($cpAddress['line_2']) ? $cpAddress['line_2'] : null,
            'city' => $cpAddress['city'],
            'state' => $cpAddress['state'],
            'postalCode' => $cpAddress['zip'],
            'country' => 'US' //TODO: If users from other countries come on board, this will
                              //      need to be changed to a field.
        ];
    }

    /**
     * @param array $order
     * @return array
     */
    public static function transformCPOrderToSSOrder($order): array
    {
        $order = collect($order)->all();

        $items = [];
        $customerUserName = $order['buyer_first_name'] . " " . $order['buyer_last_name'];

        if( array_key_exists('lines', $order)){
            $items = collect($order['lines'])->map(function($line) use($customerUserName){
                return self::transformCPOrderItemToSSOrderItem(collect($line)->toArray(), $customerUserName);
            });
        }else{
            \Log::error('Order has no items');
            \Log::info(json_encode($order));
            die();
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

    /**
     * @param array $orderItem
     * @return array
     */
    public static function transformCPOrderItemToSSOrderItem(array $orderItem): array
    {
        //During testing, order item is already transformed. That is a flaw that can
        //be fixed by having both an SSOrderFactory and CPOrderFactory so that the
        //test pushes the correct order type.
        if(env('APP_DEBUG')){
            return $orderItem;
        }

        return [
            'lineItemKey' => $orderItem['id'],
            'sku' => $orderItem['manufacturer_sku'],
            'name' => $orderItem['name'],
            'quantity' => $orderItem['quantity'],
            'unitPrice' => $orderItem['price'],
            'createDate' => $orderItem['created_at'],
        ];
    }

    /**
     * @param $order
     * @param $url
     * @return array
     */
    public static function createTrackingForOrder($order, $url)
    {
        return [
            'order_id' => $order->orderId,
            'number' => $order->trackingNumber,
            'url' => '',
            'shipped_at' => $order->shipDate,
        ];
    }
}
