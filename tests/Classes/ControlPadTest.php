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
    public function setUp() : void
    {
        $this->startDate = Carbon::yesterday()->subMonth()->startOfDay();
        $this->endDate = Carbon::now();
        $this->controlPad = new ControlPadDataModel($this->startDate, $this->endDate);
        $this->shipStation = new ShipStationDataModel();
    }

    public function testCanGetTestCpOrder(){

        //Pull as single order from ControlPad dev DB.

        //If can get it, assert true

    }



    /*
    public function testCanUpdateTestCpOrder(){

    }

    public function testCanGetTestCpOrder(){

    }

    public function testCanDeleteOrder(){

    }

    public function tearDown(): void
    {
    }
*/
}
