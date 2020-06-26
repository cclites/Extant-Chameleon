<?php

namespace App\DataModels;

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
class ControlPadDataModel extends BaseDataModel
{
    public $startDate;
    public $endDate;
    public $controlPad;
    public $headers;
    public $client;

    public function __construct(?Carbon $startDate, ?Carbon $endDate)
    {
        parent::boot();

        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->controlPad = new ControlPad();
        $this->headers = $this->controlPad->getHeader();
        $this->client = new Client();
    }

    /**
     * @param string $status
     * @return array|mixed
     */
    public function get(string $status = ControlPad::DEFAULT_STATUS)
    {
        $fullUrl = $this->CpBasePath . '/orders?start_date=' . $this->startDate .
                   '&end_date=' . $this->endDate . '&status=' . $status .
                   '&orderlines=1';

        $response = $this->client->request(
            'GET',
            $fullUrl,
            [
                'debug' => env('APP_DEBUG'),
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
                $result = $this->client->request(
                    'PATCH',
                    $this->CpBasePath . '/orders/' . $id,
                    [
                        'debug' => env('APP_DEBUG'),
                        'json' => [
                            'status' => $status,
                            'ids' => $ids
                        ],
                        'headers' => $this->headers
                    ]
                );

                return ($result->getStatusCode() === 200) ? true : false;

            }catch (GuzzleException $e){
                \Log::error("Unable to patch order: $id");
                \Log::info($e->getMessage());
                return false;
            }
        }
    }

    /**
     * @param $SsOrder
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function addTracking($SsOrder): bool
    {
        try{
            $result = $this->client->request(
                'POST',
                $this->CpBasePath . '/orders/',
                [
                    'debug' => env('APP_DEBUG'),
                    'json' => [
                        'order_id' => $SsOrder['id'],
                        'number' => $SsOrder['receipt_id'],
                        'url' => $this->CpBasePath . "/orders/" . $SsOrder['id'],
                        'shipped_at' => $SsOrder['created']
                    ],
                    'headers' => $this->headers
                ]
            );

            return ($result->getStatusCode() === 200) ? true : false;

        }catch (GuzzleException $e){
            \Log::error("Unable to add Tracking to order: " . $SsOrder['id']);
            \Log::info($e->getMessage());
            return false;
        }
    }

    public function addWebHook($webhook = "ORDER_NOTIFY"): bool
    {
        try{
            $result = $this->client->request(
                'POST',
                $this->CpBasePath . '/webhooks/subscribe',
                [
                    'debug' => env('APP_DEBUG'),
                    'json' => [
                        'event' => $webhook,
                        'target_url' => 'http://extant.digital/sscp/api/shipstation/notify-shipped',
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

    public function isLive()
    {
        //TODO: figure out a way to query ShipStation to see if it is alive
        return true;
    }
}
