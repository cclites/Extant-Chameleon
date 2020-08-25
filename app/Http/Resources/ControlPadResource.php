<?php

namespace App\Http\Resources;

use App\Tracking;
use App\Transformers\CpBuyerToSeRecipients;
use App\Transformers\CpOrderItemToSeOrderItemTransformer;
use App\Transformers\CpOrderItemToSsOrderItemTransformer;
use App\Transformers\CpToSeTransformer;
use App\Transformers\CpAddressToSsAddressTransformer;
use App\Transformers\CpToSsTransformer;
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
        return CpAddressToSsAddressTransformer::transform($cpAddress, $customerName);
    }

    /**
     * transforms a control pad order to a ship station order
     * @param array $order
     * @return array
     */
    public static function transformCPOrderToSSOrder($orders): array
    {
        if(!is_array($orders)){
            $orders = array($orders);
        }

        return CpToSsTransformer::transform($orders);
    }


    public static function transformCPOrderItemToSSOrderItem(array $orderItem): array
    {
        return CpOrderItemToSsOrderItemTransformer::transform($orderItem);
    }

    /**
     * @param $order
     * @param $url
     * @return array
     */
    public static function createTrackingForShipmentFromSS($shipment)
    {
        return [
            'order_id' => $shipment->orderNumber,
            'number' => $shipment->trackingNumber,
            'url' => Tracking::getTrackingUrlforSS($shipment),
            'shipped_at' => $shipment->shipDate,
        ];
    }

    public static function createTrackingForShipmentFromSE($shipment)
    {
        $trackingUrl =  Tracking::getTrackingUrlForSe($shipment);
        $order = collect($shipment['orders'][0]);

        return [
            'order_id' => $order['external_order_identifier'],
            'number' => $shipment['tracking_number'],
            'url' => $trackingUrl,
            'shipped_at' => $shipment['ship_date'],
        ];
    }

    /**
     * Convert a ControlPad order to a ShippingEasy
     *
     * @param array $order
     */
    public static function transformCPOrderToSEOrder(array $order)
    {
        /*
        if(!array_key_exists('lines', $order)){
            \Log::error('Order has no items');
            //\Log::info(json_encode($order));
            return '';
        }*/

        $customerUserName = $order['buyer_first_name'] . " " . $order['buyer_last_name'];

        return CpToSeTransformer::transform($order, $customerUserName);

    }

    public static function transformCPOrderItemToSEOrderItem(array $items):array
    {
        return CpOrderItemToSeOrderItemTransformer::transform($items);
    }

    public static function transformCPRecipientToSERecipient($order): array
    {
        return CpBuyerToSeRecipients::transform([$order]);
    }
}

