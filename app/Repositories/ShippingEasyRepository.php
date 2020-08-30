<?php

namespace App\Repositories;

use App\ControlPad;
use App\Http\Resources\ControlPadResource;
use App\ShippingEasy;

use App\ShipStation;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;


/**
 * Class ShipStationDataModel
 * @package App\DataModels
 *
 * NOTE: ShipStationDataModel is not a model representing a collection,
 *       but rather a representation of a ShipStation order.
 *
 *       This file contains API calls for ShipStationDataModel orders.
 *
 * @function post (add SS orders)
 */
class ShippingEasyRepository extends BaseDataModelRepository
{
    public $maxAllowedRequests;
    public $remainingRequests;
    public $secondsUntilReset;
    public $shippingEasy;
    public $headers;
    public $client;
    public $authConfigs;

    public function __construct($authConfig)
    {
        parent::boot();
        $this->authConfigs = $authConfig;

        //require_once "app/Libraries/integration_wrappers/ShippingEasy/lib/ShippingEasy.php";

        //\ShippingEasy::setApiKey($this->authConfigs['ApiKey']);
        //\ShippingEasy::setApiSecret($this->authConfigs['ApiSecret']);

    }

    /**
     * Add an order to ShippingEasy
     *
     * @param $orders
     * @return bool
     */
    public function post($orders): bool
    {
        \ShippingEasy::setApiKey($this->authConfigs['ApiKey']);
        \ShippingEasy::setApiSecret($this->authConfigs['ApiSecret']);

        foreach($orders as $order){
            $orderRequest = new \ShippingEasy_Order($this->authConfigs['StoreApiKey'], $order);
            $response = $orderRequest->create();
        }
        
        return true;
    }

    /**
     * Get order information from ShipStation
     *
     * @param string $path
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getTrackingResources(string $path)
    {
        $response = $this->client->request(
            'GET',
            $path
        );
        $responseBody = json_decode($response->getBody());
        return collect($responseBody->shipments)->map(function($shipment) {
            return ControlPadResource::createTrackingForShipmentFromSE($shipment);
        });
    }

    /**
     * Generate an array of orders and wrap in a SE create-order request
     *
     * @param array $orders
     * @return array
     */
    public function formatOrders(array $orders)
    {

        if(!filled($orders)){
            \Log::error("There really should be orders here");
            die("There really should be orders here. ShippingEasyRepository:formatOrders");
        }


        return collect($orders)->transform(function($order){

            return ControlPadResource::transformCPOrderToSEOrder($order);

        });

        //dump($orders);
        //die("Died in ShippingEasyRepository:formatOrders\n");


        //Error check to trap malformed orders
        /*
        $orders = collect($orders)->transform(function($order){

            $valid = true;

            foreach($order->lines as $line){
                if(!$line->items){
                    echo "*********************** ORDER HAS NO ITEMS\n";
                    $valid = false;
                    break;
                }
            }

            if($valid){
                echo "***************************  ORDER IS VALID\n";
                return ControlPadResource::transformCPOrderToSEOrder(collect($order)->toArray());
            }else{
                return null;
            }

        });

        return array_values(array_filter($orders->toArray()));*/
    }

    /**
     * Get the maximum number of requests that can be sent per window
     *
     * @return int
     */
    public function getMaxAllowedRequests()
    {
        return $this->maxAllowedRequests;
    }

    /**
     * Get the remaining number of requests that can be sent in the current window
     *
     * @return int
     */
    public function getRemainingRequests()
    {
        return $this->remainingRequests;
    }

    /**
     * Get the number of seconds remaining until the next window begins
     *
     * @return int
     */
    public function getSecondsUntilReset()
    {
        return $this->secondsUntilReset;
    }

    /**
     * Are we currently rate limited?
     * We are if there are no more requests allowed in the current window
     *
     * @return bool
     */
    public function isRateLimited()
    {
        return $this->remainingRequests !== null && ! $this->remainingRequests;
    }

    /**
     * Check to see if we are about to rate limit and pause if necessary.
     *
     * @param Response $response
     */
    public function sleepIfRateLimited($response)
    {
        $this->maxAllowedRequests = (int) $response->getHeader('X-Rate-Limit-Limit')[0];
        $this->remainingRequests = (int) $response->getHeader('X-Rate-Limit-Remaining')[0];
        $this->secondsUntilReset = (int) $response->getHeader('X-Rate-Limit-Reset')[0];

        if (($this->secondsUntilReset / $this->remainingRequests) > 1.5 || $this->isRateLimited()) {
            sleep(1.5);
        }
    }

    public function orderData(){
        return array(
            "external_order_identifier" => "ABC-1004",
            "ordered_at" => "2014-01-16 14:37:56 -0600",
            "order_status" => "awaiting_shipment",
            "subtotal_including_tax" => "10.00",
            "total_including_tax" => "10.00",
            "total_excluding_tax" => "10.00",
            "discount_amount" => "0.00",
            "coupon_discount" => "1.00",
            "subtotal_including_tax" => "0.00",
            "subtotal_excluding_tax" => "0.00",
            "subtotal_excluding_tax" => "0.00",
            "subtotal_tax" => "0.00",
            "total_tax" => "0.00",
            "base_shipping_cost" => "0.00",
            "shipping_cost_including_tax" => "0.00",
            "shipping_cost_excluding_tax" => "0.00",
            "shipping_cost_tax" => "0.00",
            "base_handling_cost" => "0.00",
            "handling_cost_excluding_tax" => "0.00",
            "handling_cost_including_tax" => "0.00",
            "handling_cost_tax" => "0.00",
            "base_wrapping_cost" => "0.00",
            "wrapping_cost_excluding_tax" => "0.00",
            "wrapping_cost_including_tax" => "0.00",
            "wrapping_cost_tax" => "0.00",
            "notes" => "Please send promptly.",
            "billing_company" => "Acme Inc.",
            "billing_first_name" => "Fred",
            "billing_last_name" => "Jones",
            "billing_address" => "1234 Street",
            "billing_address2" => "Suite 100",
            "billing_city" => "Austin",
            "billing_state" => "TX",
            "billing_postal_code" => "78701",
            "billing_country" => "USA",
            "billing_phone_number" => "512-123-1234",
            "billing_email" => "test@test.com",
            "recipients" => array(
                array (
                    "first_name" => "Colin",
                    "last_name" => "Homenick",
                    "company" => "Wintheiser-Hickle",
                    "email" => "charles.crona@okeefe.org",
                    "phone_number" => "637-481-6505",
                    "residential" => "true",
                    "address" => "21937 Adelbert Springs",
                    "address2" => "",
                    "province" => "",
                    "state" => "CT",
                    "city" => "Terryfurt",
                    "postal_code" => "93322",
                    "postal_code_plus_4" => "1234",
                    "country" => "Andorra",
                    "shipping_method" => "Ground",
                    "base_cost" => "10.00",
                    "cost_excluding_tax" => "10.00",
                    "cost_tax" => "0.00",
                    "base_handling_cost" => "0.00",
                    "handling_cost_excluding_tax" => "0.00",
                    "handling_cost_including_tax" => "0.00",
                    "handling_cost_tax" => "0.00",
                    "shipping_zone_id" => "123",
                    "shipping_zone_name" => "XYZ",
                    "items_total" => "1",
                    "items_shipped" => "0",
                    "line_items" => array (
                        array(
                            "item_name" => "Pencil Holder",
                            "sku" => "9876543",
                            "bin_picking_number" => "7",
                            "unit_price" => "1.30",
                            "total_excluding_tax" => "1.30",
                            "weight_in_ounces" => "10",
                            "product_options" => array("pa_size"=>"large","Colour"=>"Blue"),
                            "quantity" => "1"
                        )
                    )
                )
            )
        );
    }

}
