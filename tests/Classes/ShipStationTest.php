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
        $cpOrder = [CpOrderFactory::create()];
        $convertedOrder = $this->shipStation->formatOrders($cpOrder);

        if($convertedOrder[0]['shipTo']){
            $this->assertTrue(true);
            return;
        }

        $this->assertTrue(false);
    }

}
