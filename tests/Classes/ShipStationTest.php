<?php

namespace Classes;

use Tests\TestCase;
use GuzzleHttp\Client;

use App\Repositories\ControlPadRepository;
use App\Repositories\ShipStationRepository;
use App\Libraries\factories\CpOrderFactory;

use Carbon\Carbon;


class ShipStationTest extends TestCase
{

    public $shipStationRepository;
    public $controlPadRepository;
    public $client;
    public $address;

    public function setUp() : void
    {
        parent::Setup();

        $auths = config('auths.SHIPSTATION.DEV_1');
        $this->shipStationRepository = new ShipStationRepository($auths);
    }

    public function test_can_create_ship_station_repo(): void
    {
        $this->assertNotNull($this->shipStationRepository);
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
}

