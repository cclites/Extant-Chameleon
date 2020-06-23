<?php

namespace App\DataModels;

use App\ControlPad;
use App\Http\Resources\ControlPadResource;
use App\ShipStation;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Http;

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
class ShipStationDataModel extends BaseDataModel
{
    public $maxAllowedRequests;
    public $remainingRequests;
    public $secondsUntilReset;
    public $shipStation;
    public $headers;

    public function __construct()
    {
        parent::boot();

        $this->shipStation = new ShipStation();
        $this->headers = $this->shipStation->getHeader();
    }

    public function get()
    {
        //TODO: Add API call to get ShipStation orders and handle response
    }

    public function post($orders)
    {
        $request = new Request('POST', $this->SsBasePath, $this->headers, $orders);
        //$this->sleepIfRateLimited($request);

        return json_decode($request->getBody()->getContents());
    }

    public function addUser($data){
        //TODO:: Will need to add route to controller
    }

    public function update()
    {
        //TODO: Add API call to update ShipStation orders and handle response
        //  Might need this for testing
    }

    public function isLive()
    {
        //TODO: figure out a way to query ShipStation to see if it is alive
        return true;

    }

    //Generate an array of orders and wrap in a SS create-order request
    public function formatOrders(array $orders)
    {
        return collect($orders)->map(function($order){
            return ControlPadResource::transformCPOrderToSSOrder($order);
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
