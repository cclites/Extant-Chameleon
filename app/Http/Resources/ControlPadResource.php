<?php

namespace App\Http\Resources;

use App\Tracking;
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
        // Not cool having it here, but here it is
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
     * transforms a control pad order to a ship station order
     * @param array $order
     * @return array
     */
    public static function transformCPOrderToSSOrder($order): array
    {
        $order = collect($order)->all();
        $customerUserName = $order['buyer_first_name'] . " " . $order['buyer_last_name'];

        if( !array_key_exists('lines', $order)){
            \Log::error('Order has no items');
            \Log::info(json_encode($order));
            die();
        }

        $items = collect($order['lines'])->map(function($line) use($customerUserName){
            return self::transformCPOrderItemToSSOrderItem(collect($line)->toArray(), $customerUserName);
        });

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
    public static function createTrackingForShipment($shipment)
    {
        return [
            'order_id' => $shipment->orderNumber,
            'number' => $shipment->trackingNumber,
            'url' => Tracking::getTrackingUrl($shipment),
            'shipped_at' => $shipment->shipDate,
        ];
    }

    /**
     * Convert a ControlPad order to a ShippingEasy
     *
     * @param array $order
     * @return array
     */
    public static function transformCPOrderToSEOrder($order): array
    {
        $order = collect($order)->all();

        $customerUserName = $order['buyer_first_name'] . " " . $order['buyer_last_name'];

        if(!array_key_exists('lines', $order)){
            \Log::error('Order has no items');
            \Log::info(json_encode($order));
            die();
        }

        return [
            'external_order_identifier' => $order['id'],
            'alternate_order_id' => $order['receipt_id'],
            'ordered_at' => $order['created_at'],
            'order_status' => 'awaiting_shipment',
            'total_including_tax' => $order['total_price'],
            'total_tax' => $order['total_tax'],
            'shipping_cost_including_tax' => $order['total_shipping'],
            'billing_first_name' => $order['billing_address']['name'],
            'billing_last_name' => $order['billing_address']['name'],
            'billing_address' => $order['billing_address']['street1'],
            'billing_address_2' => $order['billing_address']['street2'],
            'billing_city' => $order['billing_address']['city'],
            'billing_state' => $order['billing_address']['state'],
            'billing_postal_code' => $order['billing_address']['postalCode'],
            'billing_phone_number' => $order['billing_address']['phone'],
            'billing_country' => 'US', //TODO: If users from other countries come on board, this will need to be changed to a field.
            'recipients' => self::transformCPRecipientToSERecipient($order),
        ];
    }

    public static function transformCPOrderItemToSEOrderItem($items):array
    {
        return $items->each(function($item){
            return [
                'ext_line_item_id' => $item['id'],
                'sku' => $item['manufacturer_sku'],
                'item_name' => $item['name'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['price'],
            ];
        });
    }

    public static function transformCPRecipientToSERecipient($order): array
    {
        return [
            'recipients' => [
                'company' => $order->buyer_first_name . " " . $order->buyer_last_name,
                'first_name' => $order->buyer_first_name,
                'last_name' => $order->buyer_last_name,
                'address' => $order->shipping_address->street1,
                'address2' => $order->shipping_address->street2,
                'city' => $order->shipping_address->city,
                'state' => $order->shipping_address->state,
                'postal_code' => $order->shipping_address->postal_code,
                'email' => $order->shipping_address->email,
                'line_items' => self::transformCPOrderItemToSEOrderItem($order->items)
            ],
        ];
    }
}

