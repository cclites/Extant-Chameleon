<?php

require 'vendor/autoload.php';

use App\Repositories\ControlPadRepository;
use App\Repositories\ShipStationRepository;
use App\User;
use Carbon\Carbon;
use Tests\TestCase;

use GuzzleHttp\Client;
use Faker\Factory as Faker;

use App\Libraries\AddressFactory;
use App\Libraries\OrderLinesFactory;

class ControlPadTest extends TestCase
{
    public $startDate;
    public $endDate;
    public $controlPad;
    public $shipStation;
    public $auths;

    public function setUp() : void
    {
        parent::Setup();

        $this->auths = config('auths.SHIPSTATION.DEV_1');
        $this->startDate = config('sscp.SSCP_START_DATE');
        $this->endDate = config('sscp.SSCP_END_DATE');
        $this->controlPad = new ControlPadRepository((array)$this->auths, $this->startDate, $this->endDate );
        $this->shipStation = new ShipStationRepository($this->auths);
    }

    public function test_can_get_cp_orders()
    {
        $orders = $this->controlPad->get('pending');
        $pass = false;

        if( filled($orders) ){
            $pass = true;
        }

        $this->assertTrue($pass);
    }

    public function test_can_update_test_cp_order(): void
    {
        $orders = $this->controlPad->get('pending');

        if(!filled($orders->data)){
            $this->assertTrue(true, 'There are no pending records');
            return;
        }

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
