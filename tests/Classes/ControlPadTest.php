<?php

require 'vendor/autoload.php';

use App\DataModels\ControlPadDataModel;
use App\DataModels\ShipStationDataModel;
use Carbon\Carbon;
use Tests\TestCase;

use GuzzleHttp\Client;
use Faker\Factory as Faker;

use App\Libraries\AddressFactory;

class ControlPadTest extends TestCase
{
    public $startDate;
    public $endDate;
    public $controlPad;
    public $shipStation;

    public function setUp() : void
    {
        parent::Setup();

        $this->startDate = Carbon::yesterday()->subMonths(2)->startOfDay();
        $this->endDate = Carbon::now();
        $this->controlPad = new ControlPadDataModel($this->startDate, $this->endDate);
        $this->shipStation = new ShipStationDataModel();
    }

    public function testCanGetTestCpOrders()
    {
        $orders = $this->controlPad->get('pending');
        $pass = false;

        if( count($orders->data) > 0 ){
            $pass = true;
        }

        $this->assertTrue($pass);
    }

    public function testCanUpdateTestCpOrder(): void
    {
        $orders = $this->controlPad->get('pending');
        $orderData = collect($orders->data)->first();

        $orderId = $orderData->id;
        $pass = false;

        $response = $this->controlPad->patch([$orderId], 'unfulfilled');

        if($response){
            $pass = true;
        }

        $this->assertTrue($pass);

        $this->controlPad->patch([$orderId], 'pending');
    }

    public function tearDown(): void
    {
    }
}
