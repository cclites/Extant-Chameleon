<?php

namespace App\Http\Controllers;

use App\ControlPad;
use App\ShipStation;
use Illuminate\Http\Request;

class ShipStationController extends BaseController
{
    /**
     * @var ShipStation
     */
    public $shipStation;

    /**
     * @var ControlPad
     */
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

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * NOTE: If necessary, this logic can be easily moved into a Job queue.
     */
    public function notifyShipped(Request $request)
    {
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

    public function testConnection(){
        return response()->json(['message' => 'Test Connection Successful']);
    }
}
