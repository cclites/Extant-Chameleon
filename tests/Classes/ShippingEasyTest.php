<?php

namespace Classes;

use Tests\TestCase;
use GuzzleHttp\Client;

use App\DataModelControllers\ControlPadModelController;
use App\DataModelControllers\ShippingEasyModelController;

use SsOrderFactory;
use CpOrderFactory;
use Carbon\Carbon;


class ShippingEasyTest extends TestCase
{
    public $startDate;
    public $endDate;
    public $controlPad;
    public $shippingEasy;
    public $client;

    public function setUp() : void
    {
        parent::Setup();

        $auths = config('auths.SHIPPINGEASY.DEV_1');

        $this->startDate = config('sscp.CP_ORDERS_START');
        $this->endDate = config('sscp.CP_ORDERS_END');

        $this->shippingEasy = new ShippingEasyModelController($auths);

        $this->client = new Client(['base_uri' => config('sscp.SE_BASE_PATH'), 'headers' => $this->shippingEasy->headers]);
    }

    //NOTE: credentials for posting to ShipStation are invalid as of 8/1/2020
    /*
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
    */

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
