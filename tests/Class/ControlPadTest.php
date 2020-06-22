<?php

use App\DataModels\ControlPadDataModel;
use App\DataModels\ShipStationDataModel;
use Carbon\Carbon;
use Tests\TestCase;

class ControlPadTest extends TestCase
{
    public function setUp() : void
    {
        $this->startDate = Carbon::yesterday()->subMonth()->startOfDay();
        $this->endDate = Carbon::now();
        $this->controlPad = new ControlPadDataModel($this->startDate, $this->endDate);
        $this->shipStation = new ShipStationDataModel();
    }

    public function testCanCreateTestCpOrder(){

    }

    public function testCanUpdateTestCpOrder(){

    }

    public function testCanGetTestCpOrder(){

    }

    public function tearDown(): void
    {
    }
}
