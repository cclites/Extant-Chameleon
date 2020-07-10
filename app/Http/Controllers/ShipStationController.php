<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\DataModelControllers\ControlPadModelController;
use App\DataModelControllers\ShipStationModelController;


class ShipStationController extends BaseController
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * NOTE: If necessary, this logic can be easily moved into a Job queue.
     */
    public function notifyShipped(Request $request, $client)
    {
        if($request->resource_url){
            $authConfig = config('auths.'.$client);
            \Log::info('notifyShipped', ['client' => $client, 'config' => $authConfig]);
            if (!$authConfig) {
                \Log::warning('ShipStationController::notifyShipped client config not found', ['client' => $client]);
                abort(409, 'Client not configured');
            }

            $url = $request->resource_url;

            $shipStation = new ShipStationModelController($authConfig);
            $controlPad = new ControlPadModelController($authConfig, null, null);

            $trackingItems = $shipStation->getTrackingResources($url);
            $ids = collect($trackingItems)->pluck('order_id')->toArray();
            \Log::info('', ['trackingItems' => $trackingItems, '$ids' => $ids]);
            /***************************************************************/

            //Add tracking
            $controlPad->addTracking($trackingItems->toArray());

            //Update control pad orders
            $controlPad->patch($ids, 'fulfilled');
        }

        return response()->json(['message' => 'Notify shipped']);
    }

    public function testConnection(){

        \Log::info("Running tests");

        return response()->json(['message' => 'Test Connection Successful']);
    }
}
