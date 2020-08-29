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
    public $authConfig;

    public function __construct($authConfig)
    {
        parent::boot();
        $this->authConfig = $authConfig;
    }

    /**
     * Add an order to ShippingEasy
     *
     * @param $orders
     * @return bool
     */
    public function post($orders): bool
    {
        $orderChunks = collect($orders)->chunk(ShipStation::MAX_ORDERS_PER_CLIENT);

        foreach($orderChunks as $chunk){
            dd($chunk);
        }
        /*
        foreach($orders as $order){

            $orderRequest = new \ShippingEasy_Order($this->authConfig['StoreApiKey'], $order);
            $response = $orderRequest->create();
        }*/

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
            die();
        }

        //Error check to trap malformed orders
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

        return array_values(array_filter($orders->toArray()));
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

}
