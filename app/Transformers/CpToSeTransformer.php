<?php


namespace App\Transformers;

use Illuminate\Support\Arr;

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
            die("CpToSeTransformer::transform");
        }

        /**
         * This is a workaround to handle the difference between a test address
         * and an actual order from CP. Test addresses always come back as an array,
         * CP addresses are parsed as objects.
         */
        if(is_object($order['billing_address'])){  // Is CP address object
            $billingAddress = (array)$order['billing_address'];
        }else{ // Is test address object
            $billingAddress = $order['billing_address'];
        }

        $address2 = array_key_exists('line_2', $billingAddress) ? $billingAddress['line_2'] : '';

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
            'billing_address' => $billingAddress['line_1'],
            'billing_address_2' => $address2,
            'billing_city' => $billingAddress['city'],
            'billing_state' => $billingAddress['state'],
            'billing_postal_code' => $billingAddress['zip'],
            'billing_country' => 'US', //TODO: If users from other countries come on board, this will need to be changed to a field.
            'recipients' => [ControlPadResource::transformCPRecipientToSERecipient($order)],
        ];
    }
}
