<?php

namespace App\Repositories;

use App\ControlPad;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use phpDocumentor\Reflection\Types\Boolean;

/**
 * Class ControlPadDataModel
 * @package App\DataModels
 *
 * NOTE: ControlPadDataModel is not a model representing a collection,
 *       but rather a representation of a ControlPad order.
 *
 *       This file contains API calls for ControlPad orders.
 *
 * @function get (get CP orders)
 * @function path (update CP orders)
 */
class ControlPadRepository extends BaseDataModelRepository
{
    public $startDate;
    public $endDate;
    public $controlPad;
    public $headers;
    public $authConfig;

    public function __construct(array $authConfig, ?Carbon $startDate, ?Carbon $endDate)
    {
        parent::boot();

        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->controlPad = new ControlPad();
        $this->headers = $this->controlPad->getHeader($authConfig);
        $this->authConfig = $authConfig;
    }

    /**
     * @param string $status
     * @param object|null $seller
     * @return object
     * @throws GuzzleException
     */
    public function get(string $status = ControlPad::DEFAULT_STATUS): object
    {
        $orderTypeIds = $this->authConfig['OrderTypeIds'];
        $types = '';

        foreach ($orderTypeIds as $key=>$id){
            $types .= "&type_id[$key]=$id";
        }

        $fullUrl = $this->CpBasePath . '/orders?start_date=' . $this->startDate .
                   '&end_date=' . $this->endDate . //'&status=' . $status .
                   '&orderlines=1' . $types;

        $client = new Client();

        $response = $client->request(
            'GET',
            $fullUrl,
            [
                'headers' => $this->headers
            ]
        );

        return json_decode($response->getBody()->getContents());
    }

    /**
     * @param array $ids
     * @param string|null $status
     */
    public function patch(array $ids, ?string $status = 'pending')
    {
        $client = new Client();

        foreach ($ids as $id){
            try{
                $client->request(
                    'PATCH',
                    $this->CpBasePath . '/orders/' . $id,
                    [
                        //'debug' => env('APP_DEBUG'),
                        'json' => [
                            'status' => $status,
                        ],
                        'headers' => $this->headers
                    ]
                );
            }catch (GuzzleException $e){
                \Log::error($e, ['fingerprint' => 'Unable to patch order', 'id' => $id]);
            }
        }
    }

    /**
     * @param $trackingItems
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function addTracking(array $trackingItems)
    {
        $client = new Client();

        try{
            foreach($trackingItems as $item){

                $result = $client->request(
                                'POST',
                                $this->CpBasePath . '/tracking/',
                                [
                                    'json' => $item,
                                    'headers' => $this->headers
                                ]
                            );

                return $result->getBody()->getContents();
            }
        }catch (GuzzleException $e){
            \Log::error($e, ['fingerprint' => 'Unable to add Tracking to order', 'item' => $item]);
            return ['fingerprint' => 'Unable to add Tracking to order', 'item' => $item];
        }
    }

    /**
     * @param $trackingItems
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function addTrackingFromSE(array $trackingItems)
    {
        $client = new Client();

        try{
            foreach($trackingItems as $item){

                $result = $client->request(
                    'POST',
                    $this->CpBasePath . '/tracking/',
                    [
                        'json' => $item,
                        'headers' => $this->headers
                    ]
                );

                return $result->getBody()->getContents();
            }
        }catch (GuzzleException $e){
            \Log::error($e, ['fingerprint' => 'Unable to add Tracking to order', 'item' => $item]);
            return ['fingerprint' => 'Unable to add Tracking to order', 'item' => $item];
        }
    }

    /**
     * @param string $webhook
     * @return bool
     */
    public function addWebHook($webhook = "SHIP_NOTIFY"): bool
    {
        $client = new Client();

        try{
            $result = $client->request(
                'POST',
                $this->CpBasePath . '/webhooks/subscribe',
                [
                    'debug' => env('APP_DEBUG'),
                    'json' => [
                        'event' => $webhook,
                        'target_url' => config('sscp.SHIPSTATION.API_NOTIFICATIONS'),
                        'store_id' => null,
                        'friendly_name' => 'Order Notification'
                    ],
                    'headers' => $this->headers
                ]
            );

            return ($result->getStatusCode() === 200) ? true : false;

        }catch(GuzzleException $e){

            \Log::error("Unable to add Web Hook");
            \Log::info($e->getMessage());
            return false;
        }
    }

}
