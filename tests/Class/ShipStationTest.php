<?php

use App\DataModels\ControlPadDataModel;
use App\DataModels\ShipStationDataModel;
use Carbon\Carbon;
use Tests\TestCase;

class ShipStationTest extends TestCase
{
    public function setUp() : void
    {
        $this->startDate = Carbon::yesterday()->subMonth()->startOfDay();
        $this->endDate = Carbon::now();
        $this->controlPad = new ControlPadDataModel($this->startDate, $this->endDate);
        $this->shipStation = new ShipStationDataModel();
    }

    public function testCanCreateSsOrder(){}

    public function testCanConvertCpOrderToSsOrder(){}

    public function testNotifyShipped(){}

    public function tearDown(): void
    {
    }
}
