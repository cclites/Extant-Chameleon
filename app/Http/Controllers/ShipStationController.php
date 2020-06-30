<?php

namespace App\Http\Controllers;

use App\ControlPad;
use App\ShipStation;
use Illuminate\Http\Request;

class ShipStationController extends BaseController
{
    public $shipStation;
    public $controlPad;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->shipStation = new ShipStation();
        $this->controlPad = new ControlPad();
    }

    public function notifyShipped(Request $request)
    {
        \Log::info("NOTIFY SHIPPED");

        if($request->resource_url){

            $url = $request->resource_url;

            $trackingItems = $this->shipStation->getTrackingResource($url);
            $ids = collect($trackingItems)->pluck('id');

            //Add tracking
            $this->controlPad->addTracking($trackingItems);

            //Update control pad orders
            $this->controlPad->patch($ids, 'fulfilled');

        }

        return response()->json(['message' => 'Notify shipped']);
    }

    public function report(Request $request)
    {
        //query for CP records within a date range
    }

    public function testConnection(){
        return response()->json(['message' => 'Testing connection']);
    }
}
