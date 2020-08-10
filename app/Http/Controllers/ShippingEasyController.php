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
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * NOTE: If necessary, this logic can be easily moved into a Job queue.
     */
    public function notifyShipped(Request $request, $client)
    {
        \Log::info($request);


        if($request->input('orders')){

            $authConfig = config('auths.SHIPPINGEASY.' . strtoupper($client) );

            if (!$authConfig) {
                \Log::warning('ShippingEasyController::notifyShipped client config not found', ['client' => $client]);
                abort(409, 'Client not configured');
            }

            $trackingResponse =  Tracking::getTrackingUrlForSe($request->shipping);

            \Log::info(json_encode($trackingResponse));


            $tracking = ControlPadResource::createTrackingForShipmentFromSE($request->orders);
            Log::info(json_encode($tracking));

            $controlPad = new ControlPadRepository($authConfig, null, null);
            $controlPad->addTracking([$tracking]);

            $orderId = $request->orders[0]->external_order_identifier;
            Log::info(json_encode($orderId));

            //generate the tracking url


            /*
            $url = $request->resource_url;

            $shipStation = new ShipStationModelController($authConfig);


            $trackingItems = $shipStation->getTrackingResources($url);
            $ids = collect($trackingItems)->pluck('order_id')->toArray();
            /***************************************************************/

            //Add tracking


            //Update control pad orders
            //$controlPad->patch($ids, 'fulfilled');
        }

        return response()->json(['message' => 'Notify shipped']);
    }

    public function testConnection(){

        \Log::info("Running tests");

        return response()->json(['message' => 'Test Connection Successful']);
    }
}
