<?php

namespace App\Http\Controllers;

use App\Http\Resources\ControlPadResource;
use App\Tracking;
use Illuminate\Http\Request;

use App\Repositories\ControlPadRepository;
use App\Repositories\ShippingEasyRepository;


class ShippingEasyController extends BaseController
{
    /**
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * NOTE: If necessary, this logic can be easily moved into a Job queue.
     */
    public function notifyShipped(Request $request, $client)
    {
        if(!$request->input('shipment')){
            return response()->json(['message' => 'Notify shipped message is invalid']);
        }

        if($request->input('shipment')){

            $shipment = $request->input('shipment');
            $authConfig = config('auths.SHIPPINGEASY.' . $client );
            $trackingObjects = ControlPadResource::createTrackingForShipmentFromSE($shipment);
            $controlPadRepo = new ControlPadRepository($authConfig, null, null);

            if (!$authConfig) {
                \Log::warning('ShippingEasyController::notifyShipped client config not found', ['client' => $client]);
                abort(409, 'Client not configured');
            }

            foreach($trackingObjects as $object){
                $controlPadRepo->addTracking($object);
                $controlPadRepo->patch($object['order_id'], 'fulfilled');
            }
        }

        return response()->json(['message' => 'Notify shipped']);
    }

    public function testConnection(){

        \Log::info("Running tests");

        return response()->json(['message' => 'TestStub Connection Successful']);
    }
}
