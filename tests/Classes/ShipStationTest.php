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

    public function setUp() : void
    {
        parent::Setup();

        $this->startDate = config('sscp.SSCP_START_DATE');
        $this->endDate = config('sscp.SSCP_END_DATE');

        $this->shipStation = new ShipStationModelController();
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
