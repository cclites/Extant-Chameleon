<?php

namespace App\DataModels;

use App\ControlPad;
use Carbon\Carbon;
use GuzzleHttp\Client as Client;
//use GuzzleHttp\Psr7\Request;

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
class ControlPadDataModel
{
    public $baseUrl;
    public $apiKey;
    public $startDate;
    public $endDate;
    public $controlPad;

    public function __construct(string $baseUrl, string $apiKey, ?Carbon $startDate, ?Carbon $endDate)
    {
        $this->baseUrl = $baseUrl;
        $this->apiKey = $apiKey;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->controlPad = new ControlPad();
    }

    public function get()
    {
        $fullUrl = $this->baseUrl .
                    '/orders?per_page=3&start_date=' .
                    $this->startDate .
                    '&end_date=' .
                    $this->endDate .
                    '&status=' .
                    ControlPad::DEFAULT_STATUS;

        $client = new Client();

        $header = $this->controlPad->getHeader();

        $response = $client->request(
            'GET',
            $fullUrl,
            [
                'headers' => $header
            ]
        );

        return json_decode($response->getBody()->getContents());
    }

    public function patch(array $ids, ?string $status = 'pending')
    {

        echo "\nPATCHING\n\n";
        echo "IDS: " . json_encode($ids) . "\n";

        if(env('APP_DEBUG') === true){
            $apiKey = config('sscp.CP_DEV_API_KEY');
            $basePath = config('sscp.CP_DEV_BASE_PATH');
        }else{
            $apiKey = config('sscp.CP_API_KEY');
            $basePath = null;
        }

        $client = new Client([
            'base_uri' => $basePath,
        ]);

        $headers = $this->controlPad->getHeader();

        foreach ($ids as $id){

            $client->request(
                'PATCH',
                '/orders/' . $id,
                [
                    //'debug' => env('APP_DEBUG'),
                    'json' => [
                        'status' => $status,
                        'ids' => $ids
                    ],
                    'headers' => $headers
                ]
            );

            $fullUrl = $basePath . '/orders/' . $id . '?orderlines=1&tracking=1';

            $client->request(
                'GET',
                $fullUrl,
                [
                    'headers' => $headers,
                    'debug' => env('APP_DEBUG'),
                ]
            );
        }

    }

    public function isLive()
    {
        //TODO: figure out a way to query ShipStation to see if it is alive
        return true;
    }
}
