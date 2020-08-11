<?php

namespace App\Repositories;

use App\ControlPad;
use App\Http\Resources\ControlPadResource;
use App\ShippingEasy;

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
        foreach(collect($orders)->chunk(ShippingEasy::MAX_ORDERS_PER_CLIENT) as $order){

            $order = new \ShippingEasy_Order($this->authConfig['StoreApiKey'], $order[0]);
            $response = $order->create();
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
     * Primarily used for testing
     *
     * @param $id
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function removeSsWebHook($id): \Psr\Http\Message\ResponseInterface
    {
        /*
        $client = new Client([
            'base_uri' => $this->SsBasePath,
            'headers' => $this->headers,
        ]);

        $response = $client->delete('/webhooks/' . $id);

        return $response;
        */
    }

    /**
     * Generate an array of orders and wrap in a SS create-order request
     *
     * @param array $orders
     * @return \Illuminate\Support\Collection
     */
    public function formatOrders(array $orders)
    {
        if(!filled($orders)){
            \Log::error("There really should be orders here");
            die();
        }

        return collect($orders)->map(function($order){
            return ControlPadResource::transformCPOrderToSEOrder(collect($order)->toArray());
        });

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
