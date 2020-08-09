<?php


namespace App\Transformers;


use App\Http\Resources\ControlPadResource;

class CpToSeTransformer
{
    /**
     * @param array $cpAddress
     * @param string $customerName
     * @return array
     */
    public static function transform(array $order, string $customerName)
    {
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
            'billing_first_name' => $customerName,
            'billing_last_name' => $customerName,
            'billing_address' => $order['billing_address']['line_1'],
            'billing_address_2' => $order['billing_address']['line_2'],
            'billing_city' => $order['billing_address']['city'],
            'billing_state' => $order['billing_address']['state'],
            'billing_postal_code' => $order['billing_address']['zip'],
            'billing_country' => 'US', //TODO: If users from other countries come on board, this will need to be changed to a field.
            'recipients' => ControlPadResource::transformCPRecipientToSERecipient($order),
        ];
    }
}
