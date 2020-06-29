<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ShipStationController extends BaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //TODO: Is there already an API middleware?
        //$this->middleware('auth');
    }

    public function notifyShipped(Request $request)
    {
        \Log::info("NOTIFY SHIPPED");

        if($request->resource_url){

            $url = $request->resource_url;

            $response = $this->shipStation->getResource($url);

            \Log::info(json_encode($response));

        }

        return response()->json(['message' => 'Notify shipped']);
    }

    /**
     * {"shipments":[{"shipmentId":42265036,"orderId":79969624,"orderKey":"d-3004","userId":"ba4449a9-ec0b-46aa-b2d5-fa502122efb9","customerEmail":null,"orderNumber":"855","createDate":"2020-06-26T14:39:08.7770000","shipDate":"2020-06-26","shipmentCost":7.02,"insuranceCost":0.00,"trackingNumber":"9405511699000554135855","isReturnLabel":false,"batchNumber":null,"carrierCode":"stamps_com","serviceCode":"usps_priority_mail","packageCode":"package","confirmation":"delivery","warehouseId":419237,"voided":false,"voidDate":null,"marketplaceNotified":false,"notifyErrorMessage":null,"shipTo":{"name":"Carlo Skiles","company":"","street1":"700 VERMILLION ST APT 8","street2":"","street3":null,"city":"CENTERVILLE","state":"SD","postalCode":"57014-2130","country":"US","phone":null,"residential":null,"addressVerified":null},"weight":{"value":2.00,"units":"ounces","WeightUnits":1},"dimensions":null,"insuranceOptions":{"provider":null,"insureShipment":false,"insuredValue":0.0},"advancedOptions":{"billToParty":"4","billToAccount":null,"billToPostalCode":null,"billToCountryCode":null,"storeId":321099},"shipmentItems":null,"labelData":null,"formData":null}],"total":1,"page":1,"pages":0}

     */



    public function report(Request $request)
    {
        //query for CP records within a date range
    }

    public function testConnection(){
        return response()->json(['message' => 'Testing connection']);
    }
}
