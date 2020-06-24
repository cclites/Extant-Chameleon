<?php

namespace App\DataModels;

use App\ControlPad;
use Carbon\Carbon;
use GuzzleHttp\Client as Client;

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

    public function get(string $status = ControlPad::DEFAULT_STATUS)
    {
        $fullUrl = $this->CpBasePath .
                    '/orders?start_date=' .
                    $this->startDate .
                    '&end_date=' .
                    $this->endDate .
                    '&status=' .
                    $status .
                    '&orderlines=1';

        $response = $this->client->request(
            'GET',
            $fullUrl,
            [
                'headers' => $this->headers
            ]
        );

        return json_decode($response->getBody()->getContents());
    }

    public function patch(array $ids, ?string $status = 'pending')
    {
        foreach ($ids as $id){

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
        }

        return true;
    }

    public function isLive()
    {
        //TODO: figure out a way to query ShipStation to see if it is alive
        return true;
    }
}
