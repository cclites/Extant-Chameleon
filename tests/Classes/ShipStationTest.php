<?php

namespace Tests\Classes;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use GuzzleHttp\Client;

use App\DataModels\ControlPadDataModel;
use App\DataModels\ShipStationDataModel;

use SsOrderFactory;
use CpOrderFactory;
use Carbon\Carbon;


class ShipStationTest extends TestCase
{
    public $startDate;
    public $endDate;
    public $controlPad;
    public $shipStation;
    public $client;

    public function setUp() : void
    {
        parent::Setup();

        $this->startDate = Carbon::yesterday()->subMonth()->startOfDay();
        $this->endDate = Carbon::now();
        $this->shipStation = new ShipStationDataModel();
        $this->client = new Client(['base_uri' => config('sscp.SS_BASE_PATH'), 'headers' => $this->shipStation->headers]);
    }

    public function testCanPostSinlgeSsOrder(): void
    {
        $order = SsOrderFactory::create();
        $response = $this->client->post('orders/createorders', ['json'=>[$order]]);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testCanPostMultipleSsOrder(): void
    {
        $orders = [];
        $cnt = 3;

        for($i = 0; $i < $cnt; $i += 1){
            $orders[] = SsOrderFactory::create();
        }

        $response = $this->client->post('orders/createorders', ['json'=> $orders]);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testCanConvertCpOrderToSsOrder(): void
    {
        $cpOrder = [CpOrderFactory::create()];
        $convertedOrder = $this->shipStation->formatOrders($cpOrder);

        if($convertedOrder[0]['shipTo']){
            $this->assertTrue(true);
            return;
        }

        $this->assertTrue(false);
    }

}
