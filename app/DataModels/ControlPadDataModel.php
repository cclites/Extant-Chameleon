<?php

namespace App\DataModels;

use App\ControlPad;
use Carbon\Carbon;
use GuzzleHttp\Client as Client;
use GuzzleHttp\Psr7\Request;

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
        if(env('APP_DEBUG') === true){
            $debug = true;
        }

        if(env('APP_DEBUG') === true){
            $apiKey = config('sscp.CP_DEV_API_KEY');
        }else{
            $apiKey = config('sscp.CP_API_KEY');
        }

        //$client = new Client();

        $header = $this->controlPad->getHeader();

        //echo "\n\n" . $header . "\n";

        /*
        //$request = new Request('PATCH', config('sscp.CP_DEV_BASE_PATH') . "/orders", $header, ['status' => 'pending', 'ids' => $ids]);
        $request = new Request('PATCH',
                        config('sscp.CP_DEV_BASE_PATH') . "/orders",
                        [
                            'header' => [
                                'Content-Type' => 'application/json',
                                'APIKey' => $apiKey
                            ],
                            'body' => [
                                'status' => "pending",
                                'ids' => $ids
                            ]
                        ]);

        return json_decode($request->getBody()->getContents());
        */
        $client = new Client([
            'headers' => $header,
            'base_uri' => config('sscp.CP_DEV_BASE_PATH') . '/orders',
            'json' => [
                'status' => "pending",
                'ids' => $ids
            ]
        ]);

        $response = $client->request('PATCH');

        /*
        $client = new Client();

        $response = $client->request(
            'PATCH',
            $this->baseUrl.'/orders',
            [
                'headers' => $header,
                'status' => $status,
                'ids' => $ids
            ]
        );*/

        return json_decode($response->getBody()->getContents());
    }

    public function isLive()
    {
        //TODO: figure out a way to query ShipStation to see if it is alive
        return true;
    }
}
