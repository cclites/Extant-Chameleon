<?php

namespace App\DataModelControllers;

use App\ControlPad;
use Carbon\Carbon;
use GuzzleHttp\Client as Client;
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
class ControlPadModelController extends BaseDataModelController
{
    public $startDate;
    public $endDate;
    public $controlPad;
    public $headers;
    public $client;
    public $sellerInfo;

    public function __construct(array $sellerInfo, ?Carbon $startDate, ?Carbon $endDate)
    {
        parent::boot();

        $this->sellerInfo = $sellerInfo;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->controlPad = new ControlPad();
        $this->headers = $this->controlPad->getHeader();
        $this->client = new Client();
    }

    /**
     * @param string $status
     * @param object|null $seller
     * @return object
     * @throws GuzzleException
     */
    public function get(string $status = ControlPad::DEFAULT_STATUS, ?object $seller = null): object
    {
        $fullUrl = $this->CpBasePath . '/orders?start_date=' . $this->startDate .
                   '&end_date=' . $this->endDate . '&status=' . $status .
                   '&orderlines=1';

        if($seller){
            $fullUrl .= '&buyer_id=' . $seller->seller_id;
        }

        $response = $this->client->request(
            'GET',
            $fullUrl,
            [
                //'debug' => env('APP_DEBUG'),
                'headers' => $this->headers
            ]
        );

        return json_decode($response->getBody()->getContents());
    }

    /**
     * @param array $ids
     * @param string|null $status
     * @return bool
     */
    public function patch(array $ids, ?string $status = 'pending'): bool
    {
        foreach ($ids as $id){

            try{
                $this->client->request(
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

                return true;

            }catch (GuzzleException $e){

                \Log::error("Unable to patch order: $id");
                \Log::info($e->getMessage());
                return false;
            }
        }
    }

    /**
     * @param $trackingItems
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function addTracking($trackingItems): bool
    {
        try{
            foreach($trackingItems as $item){

                $this->client->request(
                    'POST',
                    $this->CpBasePath . '/tracking/',
                    [
                        'json' => $item,
                    ]
                );
            }

            return true;

        }catch (GuzzleException $e){

            \Log::error("Unable to add Tracking to order: ");
            \Log::info($e->getMessage());
            return false;
        }

    }

    /**
     * @param string $webhook
     * @return bool
     */
    public function addWebHook($webhook = "SHIP_NOTIFY"): bool
    {
        try{
            $result = $this->client->request(
                'POST',
                $this->CpBasePath . '/webhooks/subscribe',
                [
                    'debug' => env('APP_DEBUG'),
                    'json' => [
                        'event' => $webhook,
                        'target_url' => config('sscp.API_NOTIFICATIONS'),
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
