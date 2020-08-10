<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Laravel\Lumen\Auth\Authorizable;

class Tracking
{
    public static function getTrackingUrlForSS($shipment){

        $tracking = config('sscp.SHIPSTATION_TRACKING_URLS');

        if(Str::contains($shipment->serviceCode, 'usps')){
            return $tracking['USPS'] . $shipment->trackingNumber;
        }else if(Str::contains($shipment->serviceCode, 'ups')){
            return $tracking['UPS'] . $shipment->trackingNumber;
        }elseif(Str::contains($shipment->serviceCode, 'fedex')){
            return $tracking['FEDEX'] . $shipment->trackingNumber;
        }else{
            return "No tracking info available for " . $shipment->serviceCode;
        }

    }

    public static function getTrackingUrlForSe($shipment){

        $tracking = config('sscp.SHIPSTATION_TRACKING_URLS');

        if(Str::contains($shipment->serviceCode, 'USPS')){
            return $tracking['USPS'] . $shipment->tracking_number;
        }else if(Str::contains($shipment->serviceCode, 'UPS')){
            return $tracking['UPS'] . $shipment->tracking_number;
        }elseif(Str::contains($shipment->serviceCode, 'FEDEX')){
            return $tracking['FEDEX'] . $shipment->tracking_number;
        }else{
            return "No tracking info available for " . $shipment->serviceCode;
        }

    }
}
