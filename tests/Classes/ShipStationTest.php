<?php

namespace Classes;

use Tests\TestCase;
use GuzzleHttp\Client;

use App\DataModelControllers\ControlPadModelController;
use App\DataModelControllers\ShipStationModelController;

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
    public $auths;

    public function setUp() : void
    {
        parent::Setup();

        $this->auths = config('auths.SHIPSTATION.DEV_1');

        $this->startDate = config('sscp.CP_ORDERS_START');
        $this->endDate = config('sscp.CP_ORDERS_END');

        $this->shipStation = new ShipStationModelController($this->auths);

        $this->client = new Client(['base_uri' => config('sscp.SS_BASE_PATH'), 'headers' => $this->shipStation->headers]);
    }

    public function testCanPostSingleSsOrder(): void
    {
        $order = SsOrderFactory::create();
        $response = $this->shipStation->post([$order]);
        $this->assertTrue($response);
    }

    public function testCanPostMultipleSsOrder(): void
    {
        $orders = [];
        $cnt = 15;

        for($i = 0; $i < $cnt; $i += 1){
            $orders[] = SsOrderFactory::create();
        }

        $response = $this->shipStation->post($orders);
        $this->assertTrue($response);
    }

    public function testCanConvertCpOrderToSsOrder(): void
    {
        $cpOrder = CpOrderFactory::create();
        $convertedOrder = $this->shipStation->formatOrders([collect($cpOrder)]);

        if($convertedOrder[0]['shipTo']){
            $this->assertTrue(true);
            return;
        }

        $this->assertTrue(false);
    }

}
