<?php

namespace App\Repositories;

use App\ControlPad;
use App\Http\Resources\ControlPadResource;
use App\ShipStation;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Arr;

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
class ShipStationRepository extends BaseDataModelRepository
{

    public $maxAllowedRequests;
    public $remainingRequests;
    public $secondsUntilReset;
    public $shipStation;
    public $headers;
    public $client;

    public function __construct($authConfig)
    {
        parent::boot();

        $shipStation = new ShipStation();
        $this->headers = $shipStation->getHeader($authConfig);
        $this->client = new Client(
            [
                'base_uri' => config('sscp.SS_BASE_PATH'),
                'headers' => $this->headers
            ]);
    }

    /**
     * @param $orders
     * @return bool
     */
    public function post($orders): bool
    {
        foreach( collect($orders)->chunk(ShipStation::MAX_ORDERS_PER_CLIENT) as $order ){

            try{
                $response = $this->client->post('orders/createorders',
                    [
                        'json' => $order
                    ]
                );

            }catch (GuzzleException $e){
                \Log::info($e->getMessage());
                \Log::error("Unable to create Shipstation orders");
                return false;
           }
        }

        return true;
    }

    public function registerClient($data)
    {
        //TODO:: Placeholder for when the ability to register
        //       clients with ShipStation is allowed.
    }

    /**
     * Create order_shipped notification for ShipStation. This can also
     * be created on the ShipStation dashboard.
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function createOrderShippedWebHook(): \Psr\Http\Message\ResponseInterface
    {
        $data = [
            "target_url" => config('sscp.SHIPSTATION.API_NOTIFICATIONS'),
            "event" => "ORDER_SHIPPED",
            "store_id" => null,
            "friendly_name" => "Shipstation order shipped"
        ];

        $response = $this->client->post('webhooks/subscribe', ['json' => $data]);

        return $response;
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
            return ControlPadResource::createTrackingForShipment($shipment);
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
        $client = new Client([
            'base_uri' => $this->SsBasePath,
            'headers' => $this->headers,
        ]);

        $response = $client->delete('/webhooks/' . $id);

        return $response;
    }

    /**
     * Generate an array of orders and wrap in a SS create-order request
     *
     * @param array $orders
     * @return \Illuminate\Support\Collection
     */
    public function formatOrders($orders)
    {
        if(!filled($orders)){
            \Log::error("There really should be orders here");
            die();
        }

        return collect($orders)->transform(function($order){

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
