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

            if (!$authConfig) {
                \Log::warning('ShippingEasyController::notifyShipped client config not found', ['client' => $client]);
                abort(409, 'Client not configured');
            }

            $tracking = ControlPadResource::createTrackingForShipmentFromSE($shipment);
            $orderId = $tracking['order_id'];

            $controlPad = new ControlPadRepository($authConfig, null, null);
            $controlPad->addTracking([$tracking]);

            $controlPad->patch([$orderId], 'fulfilled');

        }

        return response()->json(['message' => 'Notify shipped']);
    }

    public function testConnection(){

        \Log::info("Running tests");

        return response()->json(['message' => 'TestStub Connection Successful']);
    }
}
